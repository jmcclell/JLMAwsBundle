<?php

namespace JLM\AwsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;

use Symfony\Component\Config\FileLocator;

use JLM\AwsBundle\Config\AwsConfigTranslator;
use JLM\AwsBundle\Aws\Common\Aws;


/**
 * JLMAwsExtension
 * 
 */
class JLMAwsExtension extends Extension
{
    private $baseClass = null;
    private $servicePrefix = null;

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'jlm_aws';
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {       
        //die(json_encode($configs, JSON_PRETTY_PRINT));
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);



        $this->baseClass = $config['aws_base_class'];

        $this->servicePrefix = $config['service_prefix'];

        if (!empty($this->servicePrefix)) {
            $this->servicePrefix .= '.';
        }

        $awsConfigTranslator = new AwsConfigTranslator();
        $awsConfig = $awsConfigTranslator->translateConfigToAwsConfig($config);

        $this->generateServices($awsConfig, $container);

        $this->registerS3StreamWrapperAlias($config, $container);        
    }

    /**
     * Checks to see if an S3 stream wrapper service has been set. If so, it aliases it to be picked up
     * by a compiler pass later on to register the S3 stream wrapper.
     * 
     * @param  array            $config    
     * @param  ContainerBuilder $container [description]
     * @return [type]                      [description]
     */
    private function registerS3StreamWrapperAlias(array $config, ContainerBuilder $container)
    {
        $s3StreamWrapper = $config['s3_stream_wrapper'];
        if(!empty($s3StreamWrapper)) {
            if ($s3StreamWrapper === true) {
                $s3StreamWrapper = 's3';
            } else {
                $s3StreamWrapper = 's3.' . $s3StreamWrapper;
            }

            $wrapperService = $this->servicePrefix . $s3StreamWrapper;

            if ($container->has($wrapperService)) {
                $container->setAlias('jlm_aws.s3_stream_wrapper_service', $wrapperService);
            } else {
                throw new \Exception("Configuration directive 's3_stream_wrapper' is set to '$s3StreamWrapper', but no S3 service is configured by that name.'");
            }            
        }
    }

    /**
     * Generates service definitions from AWS configuration array and adds them
     * to the Container
     * 
     * @param  array            $awsConfig
     * @param  ContainerBuilder $container
     * 
     * @return void
     */
    private function generateServices(array $awsConfig, ContainerBuilder $container)
    {
        $awsConfig = $this->resolveServices($awsConfig);
        $aws = new Definition($this->baseClass, array($awsConfig));
        $aws->setFactoryClass($this->baseClass);
        $aws->setFactoryMethod('factory');
        $container->setDefinition($this->servicePrefix . 'aws', $aws);

        foreach ($awsConfig['services'] as $service => $serviceConfig) {
            if (!isset($serviceConfig['class'])) {
                continue; // Skip any configurations lacking a class as they are abstract
            }

            $definition = new Definition($serviceConfig['class'], array($service));
            $definition->setFactoryService($this->servicePrefix . 'aws');
            $definition->setFactoryMethod('get');
            $definition->addTag('jlm_aws.' . $service);
            $definition->addTag('jlm_aws.aws_service_client');
            $definition->addTag('playbloom_guzzle.client'); // Integrate with ludofleury/GuzzleBundle if it's enabled
            $container->setDefinition($this->servicePrefix . $service, $definition);       
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return 'http://jasonmcclellan.io/schema/dic/jlm-aws';
    }

     /**
     * Resolves services.
     *
     * This is taken directly from Symfony\Component\DependencyInjection\Loader\YamlFileLoader
     *
     * There was no way to re-use this without some ugly hack involving writing our config out to
     * YML first on-the-fly which, while it worked, was not a better solution than simply repeating
     * this small bit of logic here.
     *
     * @param string $value
     *
     * @return Reference
     */
    private function resolveServices($value)
    {
        if (is_array($value)) {
            $value = array_map(array($this, 'resolveServices'), $value);
        } elseif (is_string($value) &&  0 === strpos($value, '@=')) {
            return new Expression(substr($value, 2));
        } elseif (is_string($value) &&  0 === strpos($value, '@')) {
            if (0 === strpos($value, '@@')) {
                $value = substr($value, 1);
                $invalidBehavior = null;
            } elseif (0 === strpos($value, '@?')) {
                $value = substr($value, 2);
                $invalidBehavior = ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
            } else {
                $value = substr($value, 1);
                $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
            }

            if ('=' === substr($value, -1)) {
                $value = substr($value, 0, -1);
                $strict = false;
            } else {
                $strict = true;
            }

            if (null !== $invalidBehavior) {
                $value = new Reference($value, $invalidBehavior, $strict);
            }
        }

        return $value;
    }
}

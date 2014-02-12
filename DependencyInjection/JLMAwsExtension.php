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
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JLMAwsExtension extends Extension
{
    const AWS_SERVICE_PREFIX = 'jlm_aws.'; // TODO: Make configurable
    const BASE_CLASS = 'JLM\AwsBundle\Aws\Common\Aws'; // TODO: Make configurable

    private $baseClass = null;
    private $servicePrefix = null;

    public function getAlias()
    {
        return 'jlm_aws';
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {       
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
    }

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
            $container->setDefinition($this->servicePrefix . $service, $definition);       
        }
    }

    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://symfony.com/schema/dic/jlm-aws';
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

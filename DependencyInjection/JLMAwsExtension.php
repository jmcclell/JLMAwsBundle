<?php

namespace JLM\AwsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Config\FileLocator;

use JLM\AwsBundle\Config\AwsConfigTranslator;
use JLM\AwsBundle\Aws\Common\Aws;
use Symfony\Component\Yaml\Dumper;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JLMAwsExtension extends Extension
{
    const AWS_SERVICE_PREFIX = 'jlm_aws.'; // TODO: Make configurable
    const BASE_CLASS = 'JLM\AwsBundle\Aws\Common\Aws'; // TODO: Make configurable

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

        //die(json_encode($config, JSON_PRETTY_PRINT));
        
        $awsConfigTranslator = new AwsConfigTranslator();
        $awsConfig = $awsConfigTranslator->translateConfigToAwsConfig($config);

        $awsServices = $this->generateServicesConfigArray($awsConfig, $awsConfigTranslator);

        //die(json_encode($awsServices,JSON_PRETTY_PRINT));
        $dumper = new Dumper();

        /*
         * We are currently doing this so we do not have to re-implement
         * the service lookup logic found in the YAMLFileLoader, however
         * this is dirty and it's far cleaner to just re-implement that
         * small bit of logic that is unlikely to change.
         */
        $yaml = $dumper->dump($awsServices);

        $tmpFile = tempnam(sys_get_temp_dir(), 'JLMAwsBundle');

        $fp = fopen($tmpFile, 'w', false);
        fwrite($fp, $yaml);
        fclose($fp);

        $loader = new Loader\YamlFileLoader($container, new FileLocator());
        $loader->load($tmpFile);
    }

    private function generateServicesConfigArray(array $awsConfig, AwsConfigTranslator $awsConfigTranslator)
    {
        $awsServices['services'] = array(
                self::AWS_SERVICE_PREFIX . 'aws' => array(
                    'class' => self::BASE_CLASS,
                    'factory_class' => self::BASE_CLASS,
                    'factory_method' => 'factory',
                    'arguments' => array($awsConfig)
                )
            );

        foreach ($awsConfig['services'] as $service => $serviceConfig) {
            if (!isset($serviceConfig['class'])) {
                continue; // Skip any configurations lacking a class as they are abstract
            }
            
            $awsServices['services'][self::AWS_SERVICE_PREFIX . $service] = array(
                    'class' => $serviceConfig['class'],
                    'factory_service' => self::AWS_SERVICE_PREFIX . 'aws',
                    'factory_method' => 'get',
                    'arguments' => array($service)
                );         
        }

        return $awsServices;
    }

    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://symfony.com/schema/dic/jlm-aws';
    }
}

<?php

namespace Discovery\Bundle\AwsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

use Aws\Common\Aws;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DiscoveryAwsExtension extends Extension
{
    const AWS_SERVICE_PREFIX = 'discovery_aws.';

    private $awsTranslationTable = array( // AWS config name as value
                'config' => array(
                    'key' => 'key',
                    'secret' => 'secret',
                    'provider_service' => 'credentials',
                    'cache_key' => 'credentials.cache.key',
                    'client_service' => 'credentials.client',
                    'region' => 'region',
                    'scheme' => 'scheme',
                    'base_url' => 'base_url',
                    'signature' => array(
                        'version' => 'signature',
                        'version_service' => 'signature',
                        'service' => 'signature.service',
                        'region' => 'signature.region'
                        ),
                    'ssl_ca' => 'ssl.certificate_authority',
                    'curl_options' => 'curl.options',
                    'request_options' => array(
                        'request_options' => 'request.options',
                        'headers' => 'headers',
                        'query' => 'query',
                        'auth' => 'auth',
                        'cookies' => 'cookies',
                        'allow_redirects' => 'allow_redirects',
                        'save_to' => 'save_to',
                        'throw_exceptions' => 'exceptions',
                        'params' => 'params',
                        'timeout' => 'timeout',
                        'connect_timeout' => 'connect_timeout',
                        'verify' => 'verify',
                        'cert' => 'cert',
                        'ssl_key' => 'ssl_key',
                        'proxy' => 'proxy',
                        'debug' => 'debug',
                        'stream' => 'stream'
                        ),
                    'command_params' => 'command.params',
                    'backoff_logger' => 'client.backoff.logger',
                    'backoff_logger_template' => 'client.backoff.logger.template',
                    ),
                'service_names' => array(
                    'auto_scaling' => 'autoscaling',
                    'cloud_formation' => 'cloudformation',
                    'cloud_front' => 'cloudfront',
                    'cloud_front_20120505' => 'cloudfront_20120505',
                    'cloud_search' => 'cloudsearch',
                    'cloud_trail' => 'cloudtrail',
                    'cloud_watch' => 'cloudwatch',
                    'data_pipeline' => 'datapipeline',
                    'direct_connect' => 'directconnect',
                    'dynamo_db' => 'dynamodb',
                    'dynamo_db_20111205' => 'dynamodb_20111205',
                    'ec2' => 'ec2',
                    'elasticache' => 'elasticache',
                    'elastic_beanstalk' => 'elasticbeanstalk',
                    'elastic_load_balancing' => 'elasticloadbalancing',
                    'elastic_transcoder' => 'elastictranscoder',
                    'emr' => 'emr',
                    'glacier' => 'glacier',
                    'kinesis' => 'kinesis',
                    'iam' => 'iam',
                    'import_export' => 'importexport',
                    'opsworks' => 'opsworks',
                    'rds' => 'rds',
                    'redshift' => 'redshift',
                    'route53' => 'route53',
                    's3' => 's3',
                    'sdb' => 'sdb',
                    'ses' => 'ses',
                    'sns' => 'sns',
                    'sqs' => 'sqs',
                    'storage_gateway' => 'storagegateway',
                    'sts' => 'sts',
                    'support' => 'support',
                    'swf' => 'swf'
                    ),
                'default_classes' => array(
                    'autoscaling' => 'Aws\Autoscaling\AutoscalingClient',
                    'cloudformation' => 'Aws\CloudFormation\CloudFormationClient',
                    'cloudfront' => 'Aws\CloudFront\CloudFrontClient',
                    'cloudfront_20120505' => 'Aws\CloudFront\CloudFrontClient',
                    'cloudsearch' => 'Aws\CloudSearch\CloudSearchClient',
                    'cloudtrail' => 'Aws\CloudTrail\CloudTrailClient',
                    'cloudwatch' => 'Aws\CloudWatch\CloudWatchClient',
                    'datapipeline' => 'Aws\DataPipeline\DataPipelineClient',
                    'directconnect' => 'Aws\DirectConnect\DirectConnectClient',
                    'dynamodb' => 'Aws\DynamoDb\DynamoDbClient',
                    'dynamodb_20111205' => 'Aws\DynamoDb\DynamoDbClient',
                    'ec2' => 'Aws\Ec2\Ec2Client',
                    'elasticache' => 'Aws\ElastiCache\ElastiCacheClient',
                    'elasticbeanstalk' => 'Aws\ElasticBeanstalk\ElasticBeanstalkClient',
                    'elasticloadbalancing' => 'Aws\ElasticLoadBalancing\ElasticLoadBalancingClient',
                    'elastictranscoder' => 'Aws\ElasticTranscoder\ElasticTranscoderClient',
                    'emr' => 'Aws\Emr\EmrClient',
                    'glacier' => 'Aws\Glacier\GlacierClient',
                    'kinesis' => 'Aws\kinesis\KinesisClient',
                    'iam' => 'Aws\Iam\IamClient',
                    'importexport' => 'Aws\ImportExport\ImportExportClient',
                    'opsworks' => 'Aws\OpsWorks\OpsWorksClient',
                    'rds' => 'Aws\Rds\RdsClient',
                    'redshift' => 'Aws\Redshift\RedshiftClient',
                    'route53' => 'Aws\Route53\Route53Client',
                    's3' => 'Aws\S3\S3Client',
                    'sdb' => 'Aws\Sdb\SdbClient',
                    'ses' => 'Aws\Ses\SesClient',
                    'sns' => 'Aws\Sns\SnsClient',
                    'sqs' => 'Aws\Sqs\SqsClient',
                    'storagegateway' => 'Aws\StorageGateway\StorageGatewayClient',
                    'sts' => 'Aws\Sts\StsClient',
                    'support' => 'Aws\Support\SupportClient',
                    'swf' => 'Aws\Swf\SwfClient'
                    )
            );
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $awsConfig = $this->translateConfigToAwsConfig($config, $container);
        $this->setupAwsServices($awsConfig, $container);
    }

    private function setupAwsServices(array $awsConfig, ContainerBuilder $container)
    {
        $awsClassMap = $this->awsTranslationTable['default_classes'];

        $awsDefinition = new Definition('Aws\Common\Aws');
        $awsDefinition->setFactoryClass('Aws\Common\Aws');
        $awsDefinition->setFactoryMethod('factory');
        $awsDefinition->setArguments(array($awsConfig));

        $container->setDefinition(self::AWS_SERVICE_PREFIX . 'aws', $awsDefinition);

        foreach ($awsConfig['services'] as $service => $serviceConfig) {
            if ($service == 'default_settings') {
                continue;
            }
            if (isset($serviceConfig['class'])) {
                $class = $serviceConfig['class'];
            } else {
                if (isset($serviceConfig['extends'])) {
                    $class = $awsClassMap[$serviceConfig['extends']];
                } else {
                    $class = $awsClassMap[$service];
                }
            }
            $serviceDefinition = new Definition($class);
            $serviceDefinition->setFactoryService(self::AWS_SERVICE_PREFIX . 'aws');
            $serviceDefinition->setFactoryMethod('get');
            $serviceDefinition->setArguments(array($service));
            $container->setDefinition(self::AWS_SERVICE_PREFIX . $service, $serviceDefinition);
        }
    }

    /**
     * Our configuration does not match the AWS configuration exactly. This
     * will transpose our configuration array into one that the AWS service
     * builder will accept.
     * 
     * @param  array $config The configuration from our Configuration object
     * @return array         The AWS compatible configuration
     */
    private function translateConfigToAwsConfig(array $config, ContainerBuilder $container)
    {
        $awsConfig = array(
                'includes' => array('_aws'), // @see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/configuration.html#using-a-custom-configuration-file
                'services' => array()
            );

        if(!empty($config['default_settings'])) {
            $awsConfig['services']['default_settings']['params'] = $this->translateConfigBlock($config['default_settings'], $container);
        }

        $aws = $this->awsTranslationTable['service_names'];
        foreach($config['services'] as $serviceName => $serviceConfig) {
            if (empty($serviceConfig)) {
                continue;
            }
            foreach ($serviceConfig as $serviceInstanceName => $serviceInstanceConfig) {
                if (!$serviceInstanceConfig['enabled']) {
                    continue;
                }

                if ($serviceInstanceName == 'default') {
                    $serviceInstanceName = $aws[$serviceName];
                    $tmpInstanceConfig = array();
                } else {
                    $alias = $serviceName . '.' . $serviceInstanceName;
                    $serviceInstanceName = $aws[$serviceName] . '.' . $serviceInstanceName;  
                    $tmpInstanceConfig = array(
                            'extends' => $aws[$serviceName],
                            'alias' => $alias
                        );                
                }
                $tmpInstanceConfig['params'] = $this->translateConfigBlock($serviceInstanceConfig, $container);
                if (isset($serviceInstanceConfig['class'])) {
                    $tmpInstanceConfig['class'] = $serviceInstanceConfig['class'];
                }
                $awsConfig['services'][$serviceInstanceName] = $tmpInstanceConfig;
            }
        }

        return $awsConfig;
    }

    /**
     * Takes an array in the form
     *     array('credentials' => array(...), 'endpoint' => array(...), 'client' => array(...) )
     * and collapses it into a single array with translated parameter
     * names for AWS. Ignores all other keys/values in the array.
     *
     *
     * TODO: Re-factor into several functions, likely in a utility
     * class rather than directly in this class
     * 
     * @return array
     */
    private function translateConfigBlock(array $block, ContainerBuilder $container)
    {
        $credentials = (isset($block['credentials'])) ? $block['credentials'] : array();
        $endpoint = (isset($block['endpoint'])) ? $block['endpoint'] : array();
        $client = (isset($block['client'])) ? $block['client'] : array();
        $config = array_merge($credentials, $endpoint, $client);
        $awsConfig = array();

        $aws = $this->awsTranslationTable['config'];

        foreach($config as $param => $value) {
            switch ($param) {
                case 'provider_service':
                case 'client_service':
                case 'version_service':
                case 'backoff_logger':
                        $awsConfig[$aws[$param]] = $this->translateServiceReference($value, $container);
                    break;
                case 'signature':
                    if (isset($value['version_service'])) {
                        $awsConfig[$aws[$param]['version_service']] = $value['version_service'];
                    } else {
                        if (isset($value['version'])) {
                            $awsConfig[$aws[$param]['version']] = $value['version'];
                        }
                        if (isset($value['service'])) {
                            $awsConfig[$aws[$param]['service']] = $value['service'];
                        }
                        if (isset($value['region'])) {
                            $awsConfig[$aws[$param]['region']] = $value['region'];
                        }
                    }
                    break;
                case 'request_options':
                    $awsRequestOptionConfig = array();
                    foreach ($value as $requestOption => $requestOptionValue) {
                        switch ($requestOption) {
                            case 'save_to':
                                    $awsRequestOptionConfig[$aws[$param][$requestOption]] = $this->translateServiceReference($requestOptionValue, $container);
                                break;
                            case 'auth':
                                $authArray = array($requestOptionValue['username'], $requestOptionValue['password']);
                                if (isset($requestOptionValue['type'])) {
                                    $authType = $requestOptionValue['type'];
                                    if ($authType === 'ntlm') {
                                        $authType = 'NTLM';
                                    } else {
                                        $authType = ucfirst($authType);
                                    }
                                    $authArray[] = $authType;
                                }
                                $awsRequestOptionConfig[$aws[$param][$requestOption]] = $authArray;
                                break;
                            case 'cert':
                            case 'ssl_key':
                                if (isset($requestOptionValue['password'])) {
                                    $awsRequestOptionConfig[$aws[$param][$requestOption]] = array($requestOptionValue['path'], $requestOptionValue['password']);
                                } else {
                                    $awsRequestOptionConfig[$aws[$param][$requestOption]] = $requestOptionValue['path'];
                                }
                                break;
                            default:
                                 $awsRequestOptionConfig[$aws[$param][$requestOption]] = $requestOptionValue;
                        }
                    }
                    $awsConfig[$aws[$param][$param]] = $awsRequestOptionConfig;
                    break;
                default:
                    $awsConfig[$aws[$param]] = $value;
            }
        }
            
        return $awsConfig;
    }

    private function translateServiceReference($referenceString, ContainerBuilder $container)
    {
        if (strlen($referenceString) < 2 || 
            $referenceString[0] != '@' || 
            $referenceString[1] == '@') {
            return $referenceString;
        } else {
            if ($referenceString[1] == '?') {
                $strategy = ContainerInterface::NULL_ON_INVALID_REFERENCE;
                $serviceName = substr($referenceString, 2);
            } else {
                $strategy = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
                $serviceName = substr($referenceString, 1);
            }

            return $container->get($serviceName, $strategy);
        }
    }
}

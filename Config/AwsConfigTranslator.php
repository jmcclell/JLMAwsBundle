<?php

namespace JLM\AwsBundle\Config;

/**
 * Utility class which aids in transforming the JLMAwsBundle's configuration
 * into an AWS configuration array that can be passed directly to the
 * AWS service factory.
 *
 * Translation happens both in terms of array structure and key names.
 *
 * Frankly, these methods won't make much sense until you understand
 * how the configuration for AWS SDK works.
 *
 * Please see: @link(https://github.com/aws/aws-sdk-php/blob/master/src/Aws/Common/Resources/aws-config.php)
 *
 * The above URL points to the default SDK configuration. If you pass nothing into the AWS
 * service locator, that is the config that is used. Any configuration that is passed in is
 * merged with that config to create a final config. We are generating a config that matches
 * the same format as that config so that it can be merged. Anything we pass in will overwrite
 * the values found in that config.
 *
 * Our 'default' service instances simply correspond to the services that are already in this config. All
 * other service instances are added and will extend one of these default instances, by default.
 */
class AwsConfigTranslator
{
	/**
	 * A lookup table to translate our key names to the AWS config key names.
	 *
	 * Only those keys which are different need to be defined.
	 *
	 * Forward slash is used to denote a path in the configuration, eg: signature/version maps to
	 *
	 *    signature:
	 *        version: {val}
	 * 
	 * @var array
	 */
	protected $configKeyNameTranslationTable = array(
        'provider_service' => 'credentials',
        'cache_key' => 'credentials.cache.key',
        'client_service' => 'credentials.client',
        'signature/version' => 'signature',
        'signature/service' => 'signature.service',
        'signature/region' => 'signature.region',
        'ssl_ca' => 'ssl.certificate_authority',
        'curl_options' => 'curl.options',
        'request_options' => 'request.options',        
        'request_options/throw_exceptions' => 'exceptions',
        'command_params' => 'command.params',
        'backoff_logger' => 'client.backoff.logger',
        'backoff_logger_template' => 'client.backoff.logger.template',
    	);

	/**
	 * A lookup table to translate the AWS service locator look up names to their properly
	 * capitalized display names. Only multi-word services need to be defined here as the
	 * rest simply follow the rule of capitalizing the first character.
	 * 
	 * @var array
	 */
	protected $properServiceNameLookupTable = array(
		'autoscaling' => 'AutoScaling',
        'cloudformation' => 'CloudFormation',
        'cloudfront' => 'CloudFront',
        'cloudsearch' => 'CloudSearch',
        'cloudtrail' => 'CloudTrail',
        'cloudwatch' => 'CloudWatch',
        'datapipeline' => 'DataPipeline',
        'directconnect' => 'DirectConnect',
        'dynamodb' => 'DynamoDb',
        'elasticache' => 'ElastiCache',
        'elasticbeanstalk' => 'ElasticBeanstalk',
        'elasticloadbalancing' => 'ElasticLoadBalancing',
        'elastictranscoder' => 'ElasticTranscoder',
        'importexport' => 'ImportExport',
        'opsworks' => 'OpsWorks',
        'storagegateway' => 'StorageGateway',
        'sdb' => 'SimpleDb'
		);

	/**
	 * [getAwsServiceName description]
	 * @param  string $name The service name per the bundle's configuration definition
	 * @return string       The translated service name (AWS service provider look up name)
	 */
	public function translateToAwsServiceName($bundleConfigName)
	{
		return preg_replace('/_([^0-9])/', '$1', $bundleConfigName); // remove underscores except for if followed by a number such as with cloud_front_20120505 which should become cloudfront_20120505
	}

	/**
	 * Returns the marketing name for a particular service, ie: the properly capitalized service
	 * name used for display. This name is also used as part of the class path for each service
	 * client.
	 * 
	 * @param  string $lookupName The all lower-case, spaceless service name
	 * @return string     		  The properly capitalized display name
	 */
	public function getProperAwsServiceName($serviceName)
	{
		// If the lookup name has an underscore, cut it off and anything after it. (eg: cloudfront_20120505)
		$serviceNameParts = explode('_', $serviceName);
		$serviceName = $serviceNameParts[0];

		// Check if it's in the look up table, otherwise just capitalize the first letter
		return (isset($this->properServiceNameLookupTable[$serviceName])) ? 
			$this->properServiceNameLookupTable[$serviceName] :
			ucfirst($serviceName);

	}

	/**
	 * Returns the AWS client class FQN given a service lookup name, eg: ec2
	 * 
	 * @param  string $name The client service lookup name
	 * @return string       The fully qualified class name
	 */
	public function getDefaultAwsClassByServiceName($name)
	{
		$properName = $this->getProperAwsServiceName($name);

		return "Aws\\{$properName}\\{$properName}Client";
	}

    /**
     * Does a translation lookup for a config key
     * 
     * @param  string $configKey The bundle config key
     * @return string            The translated AWS config key
     */
    protected function translateConfigKey($configKey)
    {
    	return (isset($this->configKeyNameTranslationTable[$configKey])) ?
    		$this->configKeyNameTranslationTable[$configKey] :
    		$configKey;
    }

	/**
     * Our configuration does not match the AWS configuration exactly. This
     * will transpose our configuration array into one that the AWS service
     * builder will accept.
     * 
     * @param  array $config The configuration from our Configuration object
     * @return array         The AWS compatible configuration
     */
    public function translateConfigToAwsConfig(array $config)
    {
    	// Setup initial configuration array
        $awsConfig = array(
                'includes' => array('_aws'), // @see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/configuration.html#using-a-custom-configuration-file
                'services' => array()
            );

        if(!empty($config['default_settings'])) {          
            $awsConfig['services']['default_settings']['params'] = $this->translateConfigBlock($config['default_settings']);
        }

        if(!empty($config['services'])) {
            foreach($config['services'] as $serviceName => $serviceConfig) {
                if (empty($serviceConfig)) {
                    continue; // We only generate config for services that included configuration parameters
                }
                $awsConfig['services'] = array_merge($awsConfig['services'], 
                	$this->translateServiceConfigBlock($serviceName, $serviceConfig));
            }
        }

        return $awsConfig;
    }

    /**
     * Translates the configuration block for a particular service, eg:
     *
     *     services:
     *         ec2:
     *             {instance1}: ...
     *             {instance2}: ...
     *             
     * @param  string $serviceType   The service type (eg: ec2, s3, etc.) we are configuring 
     * @param  array $serviceConfig  The bundle configuration for the service type
     * @return array                 An AWS configuration array for the service type, containing all the instances for that type
     */
    protected function translateServiceConfigBlock($serviceType, $serviceConfig)
    {
    	$awsServiceConfig = array();

        foreach ($serviceConfig as $serviceInstanceName => $serviceInstanceConfig) {
            if (!$serviceInstanceConfig['enabled']) {
                continue;
            }

            $translatedInstanceConfig = array(
            	'params' => $this->translateConfigBlock($serviceInstanceConfig)
            	);

            if (isset($serviceInstanceConfig['class'])) {
                $translatedInstanceConfig['class'] = $serviceInstanceConfig['class'];
            }    


            $translatedServiceType = $this->translateToAwsServiceName($serviceType); 
            
            if (isset($serviceInstanceConfig['extends'])) {
        		$translatedInstanceConfig['extends'] = $serviceType . '.' . $serviceInstanceConfig['extends'];
        	} else {
        		if ($serviceInstanceName !== 'default') {
        			$translatedInstanceConfig['extends'] = $translatedServiceType;	
        		}        	    
        	} 

            if ($serviceInstanceName === 'default') {
                $serviceInstanceName = $serviceType;
            } else {            	            
                $serviceInstanceName =  $serviceType . '.' . $serviceInstanceName;                               
                $translatedInstanceConfig['alias'] = $translatedServiceType . '.' . $serviceInstanceName;
            }

            $awsServiceConfig[$serviceInstanceName] = $translatedInstanceConfig;       
        }

        return $awsServiceConfig;
    }

    /**
     * Takes an array in the form
     *     array('credentials' => array(...), 'endpoint' => array(...), 'client' => array(...) )
     * and collapses it into a single array with translated parameter
     * names and structure for AWS to digest.
     * 
     * Ignores all other first-level keys/values in the array.
     * 
     * @return array
     */
    protected function translateConfigBlock(array $block)
    {
        $credentials = (isset($block['credentials'])) ? $block['credentials'] : array();
        $endpoint = (isset($block['endpoint'])) ? $block['endpoint'] : array();
        $client = (isset($block['client'])) ? $block['client'] : array();

        $config = array_merge($credentials, $endpoint, $client);
        $awsConfig = array();

        foreach($config as $param => $value) {
        	$awsParam = $this->translateConfigKey($param);

            switch ($param) {
                case 'signature':                		
                		$awsConfig = array_merge($awsConfig, $this->translateSignatureConfigBlock($param, $value));
                    break;
                case 'request_options':
                    	$awsConfig = array_merge($awsConfig, $this->translateRequestOptionsConfigBlock($param, $value));
                    break;
                default:
                    $awsConfig[$awsParam] = $value;
            }
        }

        return $awsConfig;
    }

    /**
     * Translates the signature configuration block
     *
     * @param  string $topLevelParam The top level bundle configuration parameter that contains the block
     * @param  array  $configBlock   The bundle configuration
     * @return array                 The translated AWS configuration
     */
    protected function translateSignatureConfigBlock($topLevelParam, $configBlock)
    {
    	$awsConfig = array();

    	// We only use one of these. version_service overrides version.
    	if (isset($configBlock['version_service'])) {
    		$configBlock['version'] = $configBlock['version_service'];
    		unset($configBlock['version_service']);
    	}	
    	
    	foreach ($configBlock as $param => $value) {
    		$awsParam = $this->translateConfigKey($topLevelParam . '/' . $param);
    		$awsConfig[$awsParam] = $value;
    	}    	

        return $awsConfig;
    }

    /**
     * Translates the request options block
     *
     * @param  string $topLevelParam The top level bundle configuration parameter that contains the block
     * @param  array $configBlock The bundle configuration
     * @return array              The translated AWS configuration
     */
    protected function translateRequestOptionsConfigBlock($topLevelParam, $configBlock)
    {
		$requestOptions = array();

		$translatedTopLevelParam = $this->translateConfigKey($topLevelParam);

        foreach ($configBlock as $requestOption => $requestOptionValue) {
        	$translatedRequestOption = $this->translateConfigKey($topLevelParam . '/' . $requestOption);
            switch ($requestOption) {
                case 'auth':
                    $authArray = array($requestOptionValue['username'], $requestOptionValue['password']);
                    if (isset($requestOptionValue['type'])) {                       
                        $authArray[] = $this->normalizeAuthType($requestOptionValue['type']);
                    }
                    $requestOptions[$translatedRequestOption] = $authArray;
                    break;
                case 'cert':
                case 'ssl_key':
                    if (isset($requestOptionValue['password'])) {
                        $requestOptions[$translatedRequestOption] = array($requestOptionValue['path'], $requestOptionValue['password']);
                    } else {
                        $requestOptions[$translatedRequestOption] = $requestOptionValue['path'];
                    }
                    break;
                default:
                     $requestOptions[$translatedRequestOption] = $requestOptionValue;
            }
        }

        return array($translatedTopLevelParam => $requestOptions);
    }

    /**
     * Normalizes the auth type under request options by giving it proper captialization
     * @param  string $authType The given auth type
     * @return string           The normalized auth type
     */
    protected function normalizeAuthType($authType)
    {
    	$authType = strtolower($authType);
    	if ($authType === 'ntlm') {
            $authType = 'NTLM';
        } else {
            $authType = ucfirst($authType);
        }
    	return $authType;
    }
}
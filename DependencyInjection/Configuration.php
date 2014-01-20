<?php

namespace Discovery\Bundle\AwsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Service definitions that we are going to provide configuration for
     * 
     * @var array
     */
    private $availableAwsServices = array(
            'autoscaling', 'cloud_formation',
            'cloud_front', 'cloud_front_20120505',
            'cloud_search', 'cloud_trail',
            'cloud_watch', 'data_pipeline',
            'direct_connect', 'dynamo_db',
            'dynamo_db_20111205', 'ec2',
            'elasticache', 'elastic_beanstalk',
            'elastic_load_balancing', 'elastic_transcoder',
            'emr', 'glacier', 'kinesis', 'iam',
            'import_export', 'opsworks', 'rds', 
            'redshift', 'route53', 's3', 'sdb',
            'ses', 'sns', 'sqs', 'storage_gateway', 
            'sts', 'support', 'swf'
        );
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jlm_aws');

        $rootNode
            ->children()
                ->arrayNode('default_settings')
                    ->info('Default settings applied to all services. These override the SDK\'s defaults. (@see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/configuration.html)')
                    ->append($this->getCredentialConfigNode())
                    ->append($this->getEndpointConfigNode())
                    ->append($this->getClientConfigNode())
                    ->end()               
                ->end()
                ->append($this->getServicesConfigNode())
            ->end();

        return $treeBuilder;
    }

    private function getCredentialConfigNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('credentials');

        $node            
            ->children()
                ->scalarNode('key')                            
                    ->info('A valid AWS access key ID')
                    ->example('YOUR_AWS_ACCESS_KEY_ID')
                ->end()
                ->scalarNode('secret')                            
                    ->info('AWS secret for AWS access key ID')
                    ->example('YOUR_AWS_SECRET_ACCESS_KEY')
                ->end()  
                ->scalarNode('provider_service')
                    ->info('Service that will provide credentials. Instance of Aws\Common\Credentials\Credentials.')  
                    ->example('@my_provider_service')
                ->end()
                ->scalarNode('cache_key')
                    ->info('Optional custom cache key to use with the credentials')
                ->end()
                ->scalarNode('client_service')
                    ->info('Optional custom Guzzle client interface to use if your credentials require an HTTP request to acquire, sucha s with RefreshableInstanceProfileCredentials. Must be a service reference to an instance of Guzzle\Http\ClientInterface.')            
                    ->example('@my_guzzle_client')
                ->end()
            ->end();

        return $node;
    }

    private function getEndpointConfigNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('endpoint');

        $node
            ->children()
                ->scalarNode('region')                            
                    ->info('Region')
                    ->example('us-east-1')
                ->end()
                ->enumNode('scheme')                            
                    ->info('Scheme to use for HTTP requests')
                    ->example('true')
                    ->values(array('http', 'https'))
                ->end()
                ->scalarNode('base_url')
                    ->info('Allows you to specify a custom endpoint instead of having the SDK build one automatically from the region and scheme')
                    ->example('http://mycustom.com/endpoint')
                ->end()
                ->arrayNode('signature')
                    ->info('Overrides the signature used by the client')
                    ->children()
                        ->enumNode('version')                            
                            ->values(array('v4', 'v3https', 'v2'))
                            ->example('v4')
                        ->end()
                        ->scalarNode('version_service')
                            ->info('Rather than defining the version explicitly, provide a service reference that is an instance of Aws\Common\Signature\SignatureInterface in order to provide the signature version')
                            ->example('@my_signature_service')
                        ->end()
                        ->scalarNode('service')
                            ->info('Not to be confused with version_service, this is the service scope used for Signature V4.')
                        ->end()
                        ->scalarNode('region')
                            ->info('The signature region scope for Signature V4')
                        ->end()                        
                    ->end()                   
                ->end()
            ->end();

        return $node;
    }

    private function getClientConfigNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('client');

        $node
            ->children()
                ->scalarNode('ssl_ca')
                    ->info('Set to false to disable SSL. Otherwise, true to use the SDK bundled SSL cert bundle, \'system\' to use the bundle on your system, or a string pointing to the path of a specific cert file or directory of cert files.')
                    ->example('system')
                ->end()
                ->arrayNode('curl_options')                    
                    ->info('Associative array of cURL options to apply to every request created by the client. Strings will be converted to matching cURL PHP constants')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->isRequired()->end()
                    ->example(array('CURLOPT_PROXY' => 'user:password@host:port'))
                ->end()
                ->arrayNode('request_options')
                    ->info('Associate array of Guzzle reqeust options. (@see http://docs.guzzlephp.org/en/latest/http-client/client.html#request-options)')
                    ->children()
                        ->arrayNode('headers')
                            ->info('Associative array of headers to pass with each request')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->isRequired()->end()
                            ->example(array('X-Foo' => 'bar'))
                        ->end()
                        ->arrayNode('query')
                            ->info('Associative array of query string parameters to add to each request')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->isRequired()->end()
                            ->example(array('abc' => 123))
                        ->end()
                        ->arrayNode('auth')
                            ->info('Specifies HTTP authorization parameters to use with each request')
                            ->children()
                                ->scalarNode('username')
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('password')
                                    ->cannotBeEmpty()
                                ->end()
                                ->enumNode('type')                                    
                                    ->values(array('basic', 'digest', 'ntlm', 'any'))
                                ->end()

                            ->end()
                        ->end()
                        ->arrayNode('cookies')
                            ->info('Associative array of cookie values to send along with each request')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->isRequired()->end()
                            ->example(array('foo' => 'bar'))
                        ->end()
                        ->booleanNode('allow_redirects')
                        ->end()
                        ->scalarNode('save_to')
                            ->info('Path to a file where the body of a response is downloaded. Alternatively, a service reference to an instance of Guzzle\Http\EntityBodyInterface may be provided')
                            ->example('/path/to/file')
                        ->end()
                        ->booleanNode('throw_exceptions')
                            ->info('Whether or not to throw exceptions for unsuccessful HTTP response codes (eg: 404, 500, etc.)')
                        ->end()
                        ->arrayNode('params')
                            ->info('An associative array of data parameters to send along with each request. Note that these are not query parameters')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->isRequired()->end()
                            ->example(array('foo' => 'bar'))
                        ->end()
                        ->scalarNode('timeout')
                            ->info('The maximum number of seconds to allow for an entire transfer to take place before timing out')
                            ->example(20)
                        ->end()
                        ->scalarNode('connect_timeout')
                            ->info('The maximum number of seconds tow ait while trying to connect')
                            ->example(1.5)
                        ->end()
                        ->scalarNode('verify')
                            ->info('True to enable SSL, false to disable SSL, or supply the path to a CA bundle to enable verification using a custom certificate')
                            ->example(true)
                        ->end()
                        ->arrayNode('cert')
                            ->info('Allows you to specify a PEM formatted SSL client certificate to use with servers that require one')
                            ->children()
                                ->scalarNode('path')
                                    ->cannotBeEmpty()
                                    ->info('Path to PEM formatted SSL client certificate')
                                    ->example('/path/to/cert.pem')
                                ->end()
                                ->scalarNode('password')
                                    ->info('Password for PEM certificate')
                                    ->example('YOUR_PASSWORD')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('ssl_key')
                            ->info('Allows you to specify a file containing your PEM formatted private key')
                            ->children()
                                ->scalarNode('path')
                                    ->cannotBeEmpty()
                                    ->info('Path to PEM formatted private key')
                                    ->example('/path/to/key.pem')
                                ->end()
                                ->scalarNode('password')
                                    ->info('Password for your PEM private key')
                                    ->example('YOUR_PASSWORD')
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('proxy')
                            ->info('Specifies an HTTP proxy to be used for each request')
                            ->example('http://username:password@host:port')
                        ->end()
                        ->booleanNode('debug')
                            ->info('Whether or not to show verbose cURL output for each request')
                        ->end()
                        ->booleanNode('stream')
                            ->info('When using a static client you can set the sream option to true to return a GuzzlEStreamStream object')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('command_params')
                    ->info('An associative array of default options to set on each command created by the client')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->isRequired()->end()
                    ->example(array('foo' => 'bar'))
                ->end()
                ->scalarNode('backoff_logger')
                    ->info('A service reference to an instance of Guzzle\Log\LogAdapterInterface used to log backoff retries. May also be set to the string \'debug\' in order to emit PHP warnings when a retry is issued.')
                    ->example('debug')
                ->end()
                ->scalarNode('backoff_logger_template')
                    ->info('Optional template to use for exponential backoffl og messages.')
                ->end()
            ->end();

        return $node;
    }

    private function getServicesConfigNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('services');
        $node = $node->info('Enable and configure the services available in the AWS SDK');
        $children = $node->children();

        foreach ($this->availableAwsServices as $service) {
            $children = $children
                ->arrayNode($service)
                    ->useAttributeAsKey('alias')   
                    ->treatFalseLike(array('default' => false))  
                    ->treatTrueLike(array('default' => true))
                    ->treatNullLike(array('default' => true))                                   
                    ->prototype('array')
                        ->canBeEnabled()
                        ->children()
                            ->scalarNode('alias')
                                ->info('The alias for this service. Will be used exactly as written as the alias in the AWS service builder and as the Symfony service name suffix.')
                                ->cannotBeEmpty()                                
                            ->end()
                            ->scalarNode('class')
                                ->Info('Allows a custom class to be set as the service client.')
                            ->end()
                            ->append($this->getCredentialConfigNode())
                            ->append($this->getEndpointConfigNode())
                            ->append($this->getClientConfigNode())
                        ->end()
                    ->end()
                ->end();
        }
        $node->end();
        return $node;
    }
}

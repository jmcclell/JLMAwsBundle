<?php

namespace JLM\AwsBundle\Aws\Common;


use Aws\Common\Aws as BaseAws;
use Guzzle\Service\Builder\ServiceBuilderLoader;

/**
 * The base AWS class provides no way to load a full configuration
 * from an array. It forces you to load from a file. That is not
 * really feasible in our case so we are simply overriding the
 * factory method to compensate for that decision on their part.
 */
class Aws extends BaseAws
{
    /**
     * Create a new service locator for the AWS SDK
     *
     * You can configure the service locator is four different ways:
     *
     * 1. Use the default configuration file shipped with the SDK that wires class names with service short names and
     *    specify global parameters to add to every definition (e.g. key, secret, credentials, etc)
     *
     * 2. Use a custom configuration file that extends the default config and supplies credentials for each service.
     *
     * 3. Use a custom config file that wires services to custom short names for services.
     *
     * 4. If you are on Amazon EC2, you can use the default configuration file and not provide any credentials so that
     *    you are using InstanceProfile credentials.
     *
     * @param array|string $config           The full path to a .php or .js|.json file, or an associative array of data
     *                                       of configuration parameters.
     * @param array        $globalParameters Global parameters to pass to every service as it is instantiated.
     *
     * @return Aws
     */
    public static function factory($config = null, array $globalParameters = array())
    {        
        if (empty($config)) {
            // If nothing is passed in, then use the default configuration file with credentials from the environment
            $config = self::getDefaultServiceDefinition();
        }
        
        $loader = new ServiceBuilderLoader();
        $loader->addAlias('_aws', static::getDefaultServiceDefinition())
            ->addAlias('_sdk1', __DIR__  . '/Resources/sdk1-config.php');

        return $loader->load($config, $globalParameters);
    }
}
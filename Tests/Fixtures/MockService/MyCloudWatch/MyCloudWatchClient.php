<?php

namespace JLM\AwsBundle\Tests\Fixtures\MockService\MyCloudWatch;

use Aws\CloudWatch\CloudWatchClient as BaseCloudWatchClient;
use Aws\Common\Enum\ClientOptions as Options;

use Aws\Common\Client\ClientBuilder;

class MyCloudWatchClient extends BaseCloudWatchClient
{
    /**
     * Factory method to create a new Amazon CloudWatch client using an array of configuration options.
     *
     * @param array|Collection $config Client configuration data
     *
     * @return self
     * @see \Aws\Common\Client\DefaultClient for a list of available configuration options
     */
    public static function factory($config = array())
    {
        return ClientBuilder::factory(__NAMESPACE__)
            ->setConfig($config)
            ->setConfigDefaults(array(
                Options::VERSION             => static::LATEST_API_VERSION,
                Options::SERVICE_DESCRIPTION => __DIR__ . '/Resources/cloudwatch-%s.php'
            ))
            ->build();
    }
}
# Status Badges (for master)

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/jmcclell/jlmawsbundle/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
[![Build Status](https://travis-ci.org/jmcclell/JLMAwsBundle.png?branch=master)](https://travis-ci.org/jmcclell/JLMAwsBundle)
[![Coverage Status](https://coveralls.io/repos/jmcclell/JLMAwsBundle/badge.png?branch=master)](https://coveralls.io/r/jmcclell/JLMAwsBundle?branch=master)
[![Stories in Ready](https://badge.waffle.io/jmcclell/JLMAwsBundle.png?label=ready)](https://waffle.io/jmcclell/JLMAwsBundle)
[![Total Downloads](https://poser.pugx.org/jlm/aws-bundle/downloads.png)](https://packagist.org/packages/jlm/aws-bundle)
[![Latest Stable Version](https://poser.pugx.org/jlm/aws-bundle/v/stable.png)](https://packagist.org/packages/jlm/aws-bundle)

# JLMAwsBundle

This bundle serves as a fairly thin wrapper around Amazon's [AWS SDK 2.x](https://github.com/aws/aws-sdk-php) to make it easier to configure the SDK and register its services as Symfony services.

The SDK itself is built on [Guzzle](https://github.com/guzzle/guzzle) using Guzzle's service builder and configuration style. This bundle allows you to configure AWS in a more idiomatic way for a Symfony project. The Symfony configuration is then translated directly to a Guzzle configuration which is passed to the SDK in order to generate the Guzzle service clients. The Guzzle service clients are then registered as services in the Symfony DI Container for use throughout your application.

**Note:** This is a fairly new bundle and it hasn't seen much real usage yet. While there are extensive unit tests, there are likely still issues with some advanced configurations. There are also several AWS configuration options not yet supported through configuration. **All contributions are welcome!**

# Installation

This bundle can be installed via Composer by adding the following to your ```composer.json``` file:

```json
"require": {
    # ..
    "jlm/aws-bundle": "dev-master"
    # ..
}
```

Then add the bundle to your Kernel in ```AppKernel.php``` (if using Symfony Standard):

```php
public function registerBundles()
    {
        $bundles = array(
            /* ... */
            new JLM\AwsBundle\JLMAwsBundle(),
        );

        return $bundles;
    }

```

That's it! You should be able to configure the SDK via Symfony and gain access to its services from the DIC now.

# Configuration

To really understand the configuration, you should have a good understanding of how the AWS configuration looks without this bundle. We use the same names for most things and a very similar structure, but certain things are changed a bit for various reasons. Please see the [AWS Configuration documentation](http://docs.aws.amazon.com/aws-sdk-php/guide/latest/configuration.html) if you are unfamiliar.

```yaml
jlm_aws:

    # Default service prefix used for all AWS service names in the Symfony Container, eg: jlm_aws.ec2
    service_prefix:       jlm_aws # Example: jlm_aws

    # Default base class to use as the AWS factory. Note: You *must* extend the JLM version of Aws or implement a similar version as the default Aws base class is not compatible with this bundle!
    aws_base_class:       JLM\AwsBundle\Aws\Common\Aws # Example: JLM\AwsBundle\Aws\Common\Aws

    # Enables the S3 stream wrapper by either passing true (which will use the default S3 service) or by passing an S3 service name directly. Note: the service (whether default or not) *must* be enabled in the services configuration or you will receive an error!
    s3_stream_wrapper:    false # Example: my_s3|true

    # Default settings applied to all services. These override the SDK's defaults. (@see http://docs.aws.amazon.com/aws-sdk-php/guide/latest/configuration.html)
    default_settings:
        credentials:

            # A valid AWS access key ID
            key:                  ~ # Example: YOUR_AWS_ACCESS_KEY_ID

            # AWS secret for AWS access key ID
            secret:               ~ # Example: YOUR_AWS_SECRET_ACCESS_KEY

            # @Name of service that will provide credentials. Servicem ust be instance of Aws\Common\Credentials\Credentials
            provider_service:     ~ # Example: @my_provider_service

            # Optional custom cache key to use with the credentials
            cache_key:            ~

            # @Name of optional custom Guzzle client interface to use if your credentials require an HTTP request to acquire, such as with RefreshableInstanceProfileCredentials. Service must be an instance of Guzzle\Http\ClientInterface
            client_service:       ~ # Example: @my_guzzle_client
        endpoint:

            # Region
            region:               ~ # Example: us-east-1

            # Scheme to use for HTTP requests
            scheme:               ~ # One of "http"; "https", Example: true

            # Allows you to specify a custom endpoint instead of having the SDK build one automatically from the region and scheme
            base_url:             ~ # Example: http://mycustom.com/endpoint

            # Overrides the signature used by the client
            signature:
                version:              ~ # One of "v4"; "v3https"; "v2", Example: v4

                # Rather than defining the version explicitly, provide a service @name that is an instance of Aws\Common\Signature\SignatureInterface in order to provide the signature version
                version_service:      ~ # Example: @my_signature_service

                # Not to be confused with version_service, this is the service scope used for Signature V4.
                service:              ~

                # The signature region scope for Signature V4
                region:               ~
        client:

            # Set to false to disable SSL. Otherwise, true to use the SDK bundled SSL cert bundle, 'system' to use the bundle on your system, or a string pointing to the path of a specific cert file or directory of cert files.
            ssl_ca:               ~ # Example: system

            # Associative array of cURL options to apply to every request created by the client. Strings will be converted to matching cURL PHP constants
            curl_options:

                # Example:
                CURLOPT_PROXY:       user:password@host:port

                # Prototype
                name:                 ~

            # Associate array of Guzzle reqeust options. (@see http://docs.guzzlephp.org/en/latest/http-client/client.html#request-options)
            request_options:

                # Associative array of headers to pass with each request
                headers:

                    # Example:
                    X-Foo:               bar

                    # Prototype
                    name:                 ~

                # Associative array of query string parameters to add to each request
                query_params:

                    # Example:
                    abc:                 123

                    # Prototype
                    name:                 ~

                # Specifies HTTP authorization parameters to use with each request
                auth:
                    username:             ~
                    password:             ~
                    type:                 ~ # One of "basic"; "digest"; "ntlm"; "any"

                # Associative array of cookie values to send along with each request
                cookies:

                    # Example:
                    foo:                 bar

                    # Prototype
                    name:                 ~
                allow_redirects:      ~

                # Path to a file where the body of a response is downloaded. Alternatively, a service reference to an instance of Guzzle\Http\EntityBodyInterface may be provided
                save_to:              ~ # Example: /path/to/file

                # Whether or not to throw exceptions for unsuccessful HTTP response codes (eg: 404, 500, etc.)
                throw_exceptions:     ~

                # An associative array of data parameters to send along with each request. Note that these are not query parameters
                params:

                    # Example:
                    foo:                 bar

                    # Prototype
                    name:                 ~

                # The maximum number of seconds to allow for an entire transfer to take place before timing out
                timeout:              ~ # Example: 20

                # The maximum number of seconds tow ait while trying to connect
                connect_timeout:      ~ # Example: 1.5

                # True to enable SSL, false to disable SSL, or supply the path to a CA bundle to enable verification using a custom certificate
                verify:               ~ # Example: 1

                # Allows you to specify a PEM formatted SSL client certificate to use with servers that require one
                cert:

                    # Path to PEM formatted SSL client certificate
                    path:                 ~ # Required, Example: /path/to/cert.pem

                    # Password for PEM certificate
                    password:             ~ # Example: YOUR_PASSWORD

                # Allows you to specify a file containing your PEM formatted private key
                ssl_key:

                    # Path to PEM formatted private key
                    path:                 ~ # Required, Example: /path/to/key.pem

                    # Password for your PEM private key
                    password:             ~ # Example: YOUR_PASSWORD

                # Specifies an HTTP proxy to be used for each request
                proxy:                ~ # Example: http://username:password@host:port

                # Whether or not to show verbose cURL output for each request
                debug:                ~

                # When using a static client you can set the sream option to true to return a GuzzlEStreamStream object
                stream:               ~

            # An associative array of default options to set on each command created by the client
            command_params:

                # Example:
                foo:                 bar

                # Prototype
                name:                 ~

            # A service reference to an instance of Guzzle\Log\LogAdapterInterface used to log backoff retries. May also be set to the string 'debug' in order to emit PHP warnings when a retry is issued.
            backoff_logger:       ~ # Example: debug

            # Optional template to use for exponential backoffl og messages.
            backoff_logger_template:  ~

    # Enable and configure the services available in the AWS SDK
    services:
        autoscaling:

            # Prototype
            default:
                # Allows a custom class to be set as the service client.
                class:                ~

                # Extend an existing configuration. By default, the "default" for each service type is extended which in turn extends "default_settings"
                extends:              ~

                credentials:
                    # ... #
                endpoint:
                    # ... #
                client:
                    # ... #
            my_custom_autoscaler:
                # ... #
        ec2:
            default: false
            my_ec2: ~
            
        s3: ~
        # ... #
```

The general idea is that there are repeating blocks of service definitions keyed by service type and name. The first block is ```default_settings``` at the root level. These are global settings that all other services extend from (by default) and in many configurations is the only place where you'll need to add configuration such as credentials.

Below that under ```services``` is there you define all the services you wish to use. Directly below ```services``` is where you define the service type that you wish to configure. Under each service type, you may define one or more named services of that type. This allows you to have more than one S3 client, for example. Perhaps one for each region. You may optionally choose not to include any named services, but rather just set the value of the service type to ```true``` or ```null```. This will create a single default version of that service. 

The configuraiton below would enable the default version of all available services:

```yaml
jlm_aws:
  services:
    autoscaling: ~              
    cloud_formation: ~
    cloud_front: ~
    cloud_front_20120505: ~
    cloud_search: ~
    cloud_trail: ~
    cloud_watch: ~
    data_pipeline: ~
    direct_connect: ~
    dynamo_db: ~
    dynamo_db_20111205: ~
    ec2: ~
    elasticache: ~
    elastic_beanstalk: ~
    elastic_load_balancing: ~
    elastic_transcoder: ~
    emr: ~
    glacier: ~
    kinesis: ~
    iam: ~
    import_export: ~
    opsworks: ~
    rds: ~
    redshift: ~
    route53: ~
    s3: ~
    sdb: ~
    ses: ~
    sns: ~
    sqs: ~
    storage_gateway: ~
    sts: ~
    support: ~
    swf: ~
```

Each instance of a service type as well as ```default_settings``` has the exact same child options, including ```credentials```, ```endpoint```, and ```client```.

To see a full configuration from within your app, run:

```bash
$ app/console config:dump JLMAwsBundle
```

## Configuration Inheritance

There are two levels of inheritance at play with this bundle's configuration. The first is at the Symfony level. If you define a configuration for this bundle in a parent file (eg: config.yml) and then import that configuration file in a child file (eg: config_dev.yml), the child file may overwrite any configuration directives in the parent file.

There is, however, a secondary level of inheritance at the AWS SDK/Guzzle level. By providing the ```extends``` attribute to a service instance, you can inherit the settings of another instance of that type. By default, ```default_settings``` is inherited, but you could chain several S3 instances, for example, together should you choose to do so.

```yaml
jlm_aws:
  default_settings:
    credentials:
      key: MY_KEY
      secret: MY_SECRET
    endpoint:
      region: us-east-1
  services:
    s3:
      default:
        credentials:
          key: MY_S3_KEY
          secret: MY_S3_SECRET
      s3_west:
        extends: default
        endpoint:
          region: us-west-1
      s3_west_readonly:
        extends: s3_west
        credentials:
          key: READ_ONLY_KEY
          secret: READ_ONLY_SECRET
```

The above configuration would create 3 S3 services in the container:

- ```jlm_aws.s3```
- ```jlm_aws.s3.s3_west```
- ```jlm_aws.s3.s3_west_readonly```
- 

The first would have a region of ```us-east-1```inherited from ```default_settings``` but use its own ```key``` and ```secret``` values. The second would use the same as the first except it would override the region to be ```us-west-1```. Finally, the 3rd would be the same as the 2nd except it would use different credentials.

This can be pretty powerful, but in most cases the configuration will be much simpler than what it is capable of.

## S3 Wrapper

The AWS SDK provides an [S3 Wrapper](http://docs.aws.amazon.com/aws-sdk-php/guide/latest/feature-s3-stream-wrapper.html). We can automatically register a particular S3 service instance's stream wrapper by providing the ```s3_stream_wrapper``` configuration option to ```jlm_aws```. This configuraiton option accepts either a ```true|false``` value or the the name of an S3 instance that you wish to use for the stream.

Only one service can register the stream, so if you have multiple services you won't be able to register multiple streams. You could do it at runtime by unreigstering a previous stream, getting an instance of the S3 client you want, and then re-registering the stream with that instance. Obviously we cannot facilitate that in this configuration bundle, however.

```yaml
jlm_aws:
  s3_stream_wrapper: true
  services:
    s3: ~
```

The configuration above would create a default S3 client and register its stream wrapper.

**Important:** You must enable the S3 default service if you choose to use a value of ```true``` for ```s3_stream_wrapper``` as we did above by adding ```s3: ~``` to the config. You could also enable it via:

```yaml
jlm_aws:
  s3_steram_wrapper: true
  services:
    s3:
      default: ~
```

The above two configurations are equivalent. Likewise, if you choose to use a named instance, that instance must be registered under the S3 services configuration.

## Integrations

This bundle integrates with [PlaybloomGuzzleBundle](https://github.com/ludofleury/GuzzleBundle) automatically. The Playbloom bundle allows you to see Guzzle client requests in the profiler UI. The integration happens by way of a tag in the Symfony DI container and is enabled by default. Currently it cannot be disabled. It has no effect, however, unless you also include the PlayblooomGuzzleBundle in your project.



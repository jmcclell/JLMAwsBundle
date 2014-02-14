<?php

namespace JLM\AwsBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class JLMAwsExtensionTest extends WebTestCase
{

    public function getClient($env)
    {
        return static::createClient(array('environment' => $env));
    }

    public function formatDataProvider()
    {
        return array(array('xml'), /*array('yml')*/);
    }

    /**
     * Config file does not ahve namespace schema location so we avoid xml errors (and instead test the config object)
     * 
     * @dataProvider formatDataProvider
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "path" at path "jlm_aws.default_settings.client.request_options.ssl_key" must be configured.
     */
    public function testMissingPathInKey($format)
    {
        $client = static::getClient('missing_path_key_' . $format);
    }

    /**
     * Config file does not have namespace schema location so we avoid xml errors (and instead test the config object)
     * 
     * @dataProvider formatDataProvider
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "path" at path "jlm_aws.default_settings.client.request_options.cert" must be configured.
     */
    public function testMissingPathInCert($format)
    {
        $client = static::getClient('missing_path_cert_' . $format);
    }

    /**
     * @expectedException Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unable to parse
     */
    public function testMissingPathInKeyXmlCheck()
    {
        $client = static::getClient('missing_path_key_xml_validation_xml');
    }

    /**
     * @expectedException Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unable to parse
     */
    public function testMissingPathInCertXmlCheck()
    {
        $client = static::getClient('missing_path_cert_xml_validation_xml');
    }

    

    /**
     * @dataProvider formatDataProvider
     */
    public function testMinimumConfig($format)
    {
        $client = $this->getClient('minimum_' . $format);
        $container = $client->getContainer();
        $this->assertEquals('mysecret', $container->getParameter('kernel.secret'));
        $this->assertTrue($container->has('jlm_aws.aws'));

        $this->assertFalse($container->has('jlm_aws.autoscaling'));            
        $this->assertFalse($container->has('jlm_aws.cloud_formation'));
        $this->assertFalse($container->has('jlm_aws.cloud_front'));
        $this->assertFalse($container->has('jlm_aws.cloud_front_20120505'));
        $this->assertFalse($container->has('jlm_aws.cloud_search'));
        $this->assertFalse($container->has('jlm_aws.cloud_trail'));
        $this->assertFalse($container->has('jlm_aws.cloud_watch'));
        $this->assertFalse($container->has('jlm_aws.data_pipeline'));
        $this->assertFalse($container->has('jlm_aws.direct_connect'));
        $this->assertFalse($container->has('jlm_aws.dynamo_db'));
        $this->assertFalse($container->has('jlm_aws.dynamo_db_20111205'));
        $this->assertFalse($container->has('jlm_aws.ec2'));
        $this->assertFalse($container->has('jlm_aws.elasticache'));
        $this->assertFalse($container->has('jlm_aws.elastic_beanstalk'));
        $this->assertFalse($container->has('jlm_aws.elastic_load_balancing'));
        $this->assertFalse($container->has('jlm_aws.elastic_transcoder'));
        $this->assertFalse($container->has('jlm_aws.emr'));
        $this->assertFalse($container->has('jlm_aws.glacier'));
        $this->assertFalse($container->has('jlm_aws.kinesis'));
        $this->assertFalse($container->has('jlm_aws.iam'));
        $this->assertFalse($container->has('jlm_aws.import_export'));
        $this->assertFalse($container->has('jlm_aws.opsworks'));
        $this->assertFalse($container->has('jlm_aws.rds'));
        $this->assertFalse($container->has('jlm_aws.redshift'));
        $this->assertFalse($container->has('jlm_aws.route53'));
        $this->assertFalse($container->has('jlm_aws.s3'));
        $this->assertFalse($container->has('jlm_aws.sdb'));
        $this->assertFalse($container->has('jlm_aws.ses'));
        $this->assertFalse($container->has('jlm_aws.sns'));
        $this->assertFalse($container->has('jlm_aws.sqs'));
        $this->assertFalse($container->has('jlm_aws.storage_gateway'));
        $this->assertFalse($container->has('jlm_aws.sts'));
        $this->assertFalse($container->has('jlm_aws.support'));
        $this->assertFalse($container->has('jlm_aws.swf'));
    }    

    /**
     * @dataProvider formatDataProvider
     */
    public function testDefaultSettingsNoServiceRefsConfig($format)
    {
        $client = $this->getClient('default_settings_no_service_refs_' . $format);
        $container = $client->getContainer();
        $aws = $container->get('jlm_aws.aws');
        $defaultSettings = $aws->getData('default_settings');
        $defaultSettings = $defaultSettings['params'];
        $this->assertEquals('MY_KEY', $defaultSettings['key']);
        $this->assertEquals('MY_SECRET', $defaultSettings['secret']);
        $this->assertEquals('us-east-1', $defaultSettings['region']);
        $this->assertEquals('https', $defaultSettings['scheme']);
        $this->assertEquals(true, $defaultSettings['ssl.certificate_authority']);
        $this->assertTrue(empty($defaultSettings['curl.options']));
        $this->assertTrue(empty($defaultSettings['command.params']));
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testDefaultServicesCreated($format)
    {
        $client = $this->getClient('default_services_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.autoscaling'));            
        $this->assertTrue($container->has('jlm_aws.cloud_formation'));
        $this->assertTrue($container->has('jlm_aws.cloud_front'));
        $this->assertTrue($container->has('jlm_aws.cloud_front_20120505'));
        $this->assertTrue($container->has('jlm_aws.cloud_search'));
        $this->assertTrue($container->has('jlm_aws.cloud_trail'));
        $this->assertTrue($container->has('jlm_aws.cloud_watch'));
        $this->assertTrue($container->has('jlm_aws.data_pipeline'));
        $this->assertTrue($container->has('jlm_aws.direct_connect'));
        $this->assertTrue($container->has('jlm_aws.dynamo_db'));
        $this->assertTrue($container->has('jlm_aws.dynamo_db_20111205'));
        $this->assertTrue($container->has('jlm_aws.ec2'));
        $this->assertTrue($container->has('jlm_aws.elasticache'));
        $this->assertTrue($container->has('jlm_aws.elastic_beanstalk'));
        $this->assertTrue($container->has('jlm_aws.elastic_load_balancing'));
        $this->assertTrue($container->has('jlm_aws.elastic_transcoder'));
        $this->assertTrue($container->has('jlm_aws.emr'));
        $this->assertTrue($container->has('jlm_aws.glacier'));
        $this->assertTrue($container->has('jlm_aws.kinesis'));
        $this->assertTrue($container->has('jlm_aws.iam'));
        $this->assertTrue($container->has('jlm_aws.import_export'));
        $this->assertTrue($container->has('jlm_aws.opsworks'));
        $this->assertTrue($container->has('jlm_aws.rds'));
        $this->assertTrue($container->has('jlm_aws.redshift'));
        $this->assertTrue($container->has('jlm_aws.route53'));
        $this->assertTrue($container->has('jlm_aws.s3'));
        $this->assertTrue($container->has('jlm_aws.sdb'));
        $this->assertTrue($container->has('jlm_aws.ses'));
        $this->assertTrue($container->has('jlm_aws.sns'));
        $this->assertTrue($container->has('jlm_aws.sqs'));
        $this->assertTrue($container->has('jlm_aws.storage_gateway'));
        $this->assertTrue($container->has('jlm_aws.sts'));
        $this->assertTrue($container->has('jlm_aws.support'));
        $this->assertTrue($container->has('jlm_aws.swf'));
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testCustomNamedServicesCreated($format)
    {
        $client = $this->getClient('default_named_services_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.autoscaling.my_autoscaling'));            
        $this->assertTrue($container->has('jlm_aws.cloud_formation.my_cloud_formation'));
        $this->assertTrue($container->has('jlm_aws.cloud_front.my_cloud_front'));
        $this->assertTrue($container->has('jlm_aws.cloud_front_20120505.my_cloud_front_20120505'));
        $this->assertTrue($container->has('jlm_aws.cloud_search.my_cloud_search'));
        $this->assertTrue($container->has('jlm_aws.cloud_trail.my_cloud_trail'));
        $this->assertTrue($container->has('jlm_aws.cloud_watch.my_cloud_watch'));
        $this->assertTrue($container->has('jlm_aws.data_pipeline.my_data_pipeline'));
        $this->assertTrue($container->has('jlm_aws.direct_connect.my_direct_connect'));
        $this->assertTrue($container->has('jlm_aws.dynamo_db.my_dynamo_db'));
        $this->assertTrue($container->has('jlm_aws.dynamo_db_20111205.my_dynamo_db_20111205'));
        $this->assertTrue($container->has('jlm_aws.ec2.my_ec2'));
        $this->assertTrue($container->has('jlm_aws.elasticache.my_elasticache'));
        $this->assertTrue($container->has('jlm_aws.elastic_beanstalk.my_elastic_beanstalk'));
        $this->assertTrue($container->has('jlm_aws.elastic_load_balancing.my_elastic_load_balancing'));
        $this->assertTrue($container->has('jlm_aws.elastic_transcoder.my_elastic_transcoder'));
        $this->assertTrue($container->has('jlm_aws.emr.my_emr'));
        $this->assertTrue($container->has('jlm_aws.glacier.my_glacier'));
        $this->assertTrue($container->has('jlm_aws.kinesis.my_kinesis'));
        $this->assertTrue($container->has('jlm_aws.iam.my_iam'));
        $this->assertTrue($container->has('jlm_aws.import_export.my_import_export'));
        $this->assertTrue($container->has('jlm_aws.opsworks.my_opsworks'));
        $this->assertTrue($container->has('jlm_aws.rds.my_rds'));
        $this->assertTrue($container->has('jlm_aws.redshift.my_redshift'));
        $this->assertTrue($container->has('jlm_aws.route53.my_route53'));
        $this->assertTrue($container->has('jlm_aws.s3.my_s3'));
        $this->assertTrue($container->has('jlm_aws.sdb.my_sdb'));
        $this->assertTrue($container->has('jlm_aws.ses.my_ses'));
        $this->assertTrue($container->has('jlm_aws.sns.my_sns'));
        $this->assertTrue($container->has('jlm_aws.sqs.my_sqs'));
        $this->assertTrue($container->has('jlm_aws.storage_gateway.my_storage_gateway'));
        $this->assertTrue($container->has('jlm_aws.sts.my_sts'));
        $this->assertTrue($container->has('jlm_aws.support.my_support'));
        $this->assertTrue($container->has('jlm_aws.swf.my_swf'));
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testCredentialsProviderService($format)
    {
        $client = $this->getClient('credential_service_provider_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.credentials_provider'));

        $aws = $container->get('jlm_aws.aws');
        $defaultSettings = $aws->getData('default_settings');
        $defaultSettings = $defaultSettings['params'];

        // Ensure we did not remove unnecessary data, let the AWS SDK handle that logic
        $this->assertFalse(empty($defaultSettings['key']));
        $this->assertFalse(empty($defaultSettings['secret']));
        $this->assertTrue($defaultSettings['credentials'] instanceof \Aws\Common\Credentials\CredentialsInterface);

    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testSignatureProviderService($format)
    {
        $client = $this->getClient('signature_service_provider_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.signature_provider'));

        $aws = $container->get('jlm_aws.aws');
        $defaultSettings = $aws->getData('default_settings');
        $defaultSettings = $defaultSettings['params'];

        $this->assertTrue($defaultSettings['signature'] instanceof \Aws\Common\Signature\SignatureInterface);
        $this->assertEquals('MY_SERVICE', $defaultSettings['signature.service']);
        $this->assertEquals('MY_REGION', $defaultSettings['signature.region']);
    } 

    /**
     * @dataProvider formatDataProvider
     */
    public function testDisabledService($format)
    {
        $client = $this->getClient('params_' . $format);
        $container = $client->getContainer();

        $this->assertFalse($container->has('jlm_aws.autoscaling'));
        $this->assertFalse($container->has('jlm_aws.cloud_formation'));        
    }   

    /**
     * @dataProvider formatDataProvider
     */
    public function testRequestOptions($format)
    {
        $client = $this->getClient('params_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.cloud_search.custom_request_options'));

        $aws = $container->get('jlm_aws.aws');
        
        $config = $aws->getData('cloudsearch.custom_request_options');
        $config = $config['params'];
        $requestOptions = $config['request.options'];
        
        $this->assertEquals(2, $requestOptions['timeout']);
        $this->assertEquals(false, $requestOptions['verify']);
        $this->assertEquals('http://username:password@host:80', $requestOptions['proxy']);
        $this->assertEquals(true, $requestOptions['debug']);
        $this->assertEquals(true, $requestOptions['stream']);
        $this->assertEquals(true, $requestOptions['allow_redirects']);
        $this->assertEquals('/tmp', $requestOptions['save_to']);
        $this->assertEquals(true, $requestOptions['exceptions']);
        $this->assertEquals(1.5, $requestOptions['connect_timeout']);
        $this->assertEquals('bar', $requestOptions['headers']['foo']);
        $this->assertEquals('bleh', $requestOptions['headers']['blah']);
        $this->assertEquals('bar', $requestOptions['cookies']['foo']);
        $this->assertEquals('bleh', $requestOptions['cookies']['blah']);
        $this->assertEquals('bar', $requestOptions['params']['foo']);
        $this->assertEquals('bleh', $requestOptions['params']['blah']);
        $this->assertEquals('bar', $requestOptions['query']['foo']);
        $this->assertEquals('bleh', $requestOptions['query']['blah']);
        $this->assertEquals(array('/my_key', 'MY_PASSWORD'), $requestOptions['ssl_key']);
        $this->assertEquals(array('/my_cert', 'MY_PASSWORD'), $requestOptions['cert']);
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testAlias($format)
    {
        $client = $this->getClient('params_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.cloud_trail.alias'));

        $aws = $container->get('jlm_aws.aws');
        
        $config = $aws->getData('cloudtrail.alias');
        $alias = $config['alias'];
        $this->assertEquals('cloud_trail.alias', $alias);
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testClass($format)
    {
        $client = $this->getClient('params_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.cloud_watch.parent'));
        $parent = $container->get('jlm_aws.cloud_watch.parent');
        
        $this->assertTrue($parent instanceof \JLM\AwsBundle\Tests\Fixtures\MockService\MyCloudWatch\MyCloudWatchClient);        
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testExtends($format)
    {
        $client = $this->getClient('params_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.cloud_watch.parent'));
        $this->assertTrue($container->has('jlm_aws.cloud_watch.child'));

        $child = $container->get('jlm_aws.cloud_watch.child');

        $this->assertTrue($child instanceof \JLM\AwsBundle\Tests\Fixtures\MockService\MyCloudWatch\MyCloudWatchClient);
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testRequestOptionInheritance($format)
    {
        $client = $this->getClient('params_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.data_pipeline.request_options_parent'));
        $this->assertTrue($container->has('jlm_aws.data_pipeline.request_options_child'));

        $aws = $container->get('jlm_aws.aws');
        
        $child = $aws->getData('datapipeline.request_options_child');
        
        $opts = $child['params']['request.options'];

        $this->assertEquals(false, $opts['allow_redirects']);
        $this->assertEquals('/tmp_child', $opts['save_to']);
        $this->assertEquals(false, $opts['exceptions']);
        $this->assertEquals(1.5, $opts['connect_timeout']);
        $this->assertEquals(2, $opts['timeout']);
        $this->assertEquals(false, $opts['verify']);
        $this->assertEquals(null, $opts['proxy']);
        $this->assertEquals(true, $opts['debug']);
        $this->assertEquals(true, $opts['stream']);

        $this->assertEquals('/my_child_cert', $opts['cert']);
        $this->assertEquals(array('/my_child_key', 'MY_CHILD_PASSWORD'), $opts['ssl_key']);


        $this->assertEquals(array('child_only' => 'child_value', 'foo' => 'child_bar', 'blah' => 'bleh'), $opts['headers']);
        $this->assertEquals(array('child_only' => 'child_value', 'foo' => 'child_bar', 'blah' => 'bleh'), $opts['params']);
        $this->assertEquals(array('child_only' => 'child_value', 'foo' => 'child_bar', 'blah' => 'bleh'), $opts['cookies']);
        $this->assertEquals(array('child_only' => 'child_value', 'foo' => 'child_bar', 'blah' => 'bleh'), $opts['query']);
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testSymfonyConfigInheritance($format)
    {
        $client = $this->getClient('inheritance_child_' . $format);
        $container = $client->getContainer();

        $this->assertTrue($container->has('jlm_aws.ec2.my_ec2'));


        $aws = $container->get('jlm_aws.aws');
        
        $child = $aws->getData('ec2.my_ec2');
        
        // request options are the deepest we can go so it's a good overall test
        $opts = $child['params']['request.options'];

        $this->assertEquals(true, $opts['allow_redirects']);
        $this->assertEquals('/tmp_override', $opts['save_to']);
        $this->assertEquals(false, $opts['exceptions']);;
        $this->assertEquals('user:pass@proxy.com:80', $opts['proxy']);


        $this->assertEquals('/my_override_cert', $opts['cert']);
        $this->assertEquals(array('/my_override_key', 'MY_OVERRIDE_PASSWORD'), $opts['ssl_key']);


        $this->assertEquals(array('override_only' => 'override_value', 'foo' => 'override_bar', 'blah' => 'bleh'), $opts['headers']);
        $this->assertEquals(array('override_only' => 'override_value', 'foo' => 'override_bar', 'blah' => 'bleh'), $opts['params']);
        $this->assertEquals(array('override_only' => 'override_value', 'foo' => 'override_bar', 'blah' => 'bleh'), $opts['cookies']);
        $this->assertEquals(array('override_only' => 'override_value', 'foo' => 'override_bar', 'blah' => 'bleh'), $opts['query']);
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testS3StreamWrapperTrue($format)
    {
        $client = $this->getClient('s3_stream_wrapper_true_' . $format);
        $container = $client->getContainer();
        $this->assertTrue($container->has('jlm_aws.s3_stream_wrapper_service'));
        $wrappers = stream_get_wrappers();
        $this->assertTrue(in_array('s3', $wrappers));
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testS3StreamWrapperNamed($format)
    {
        $client = $this->getClient('s3_stream_wrapper_named_' . $format);
        $container = $client->getContainer();
        $this->assertTrue($container->has('jlm_aws.s3_stream_wrapper_service'));

        $wrappers = stream_get_wrappers();

        $this->assertTrue(in_array('s3', $wrappers));
    }

    /**
     * @dataProvider formatDataProvider
     * @expectedException Exception
     * @expectedExceptionMessage Configuration directive 's3_stream_wrapper' is set to 's3', but no S3 service is configured by that name.'
     */
    public function testS3StreamWrapperMissing($format)
    {
        $client = $this->getClient('s3_stream_wrapper_missing_' . $format);
        $container = $client->getContainer();
    }
}
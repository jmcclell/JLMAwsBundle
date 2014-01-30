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
     * @dataProvider formatDataProvider
     */
    public function testMinimumConfig($format)
    {
        $client = $this->getClient('minimum_' . $format);
        $container = $client->getContainer();
        $this->assertEquals('mysecret', $container->getParameter('kernel.secret'));
        $this->assertTrue($container->has('jlm_aws.aws'));
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
        $defaultSettings = $defaultSettings['params']['services']['default_settings']['params'];
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
        $defaultSettings = $defaultSettings['params']['services']['default_settings']['params'];

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
        $defaultSettings = $defaultSettings['params']['services']['default_settings']['params'];

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
        
        $config = $aws->getConfig();
        $config = $config['default_settings']['params']['services']['cloudsearch.custom_request_options'];

        $requestOptions = $config['params']['request.options'];
        
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
        
        $config = $aws->getConfig();
        $config = $config['default_settings']['params']['services']['cloudtrail.alias'];
        $alias = $config['alias'];
        $this->assertEquals('cloud_trail.alias', $alias);
    }
}
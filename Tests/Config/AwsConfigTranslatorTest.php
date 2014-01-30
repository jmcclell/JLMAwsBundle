<?php

namespace JLM\AwsBundle\Tests\Config;

use JLM\AwsBundle\Config\AwsConfigTranslator;

class AwsConfigTranslatorTest extends \PHPUnit_Framework_TestCase
{
	public $translator;

	public function setUp()
	{
		$this->translator = new AwsConfigTranslator();
	}

	/**
	 * 
	 * @dataProvider translateToAwserviceNameProvider
	 */
	public function translateToAwsServiceName($input, $expected)
	{
		$output = $this->translator->translateToAwsServiceName($input);
		$this->assertEquals($output, $expected);
	}

	public function translateToAwsServiceNameProvider()
	{
		return array(
			array('auto_scaling', 'autoscaling'),
	        array('cloud_formation', 'cloudformation'),
	        array('cloud_front', 'cloudfront'),
	        array('cloud_front_20120505', 'cloudfront'),
	        array('cloud_search', 'cloudsearch'),
	        array('cloud_trail', 'cloudtrail'),
	        array('cloud_watch', 'cloudwatch'),
	        array('data_pipeline', 'datapipeline'),
	        array('direct_connect', 'directconnect'),
	        array('dynamo_db', 'dynamodb'),
	        array('elasticache', 'elasticache'),
	        array('elastic_beanstalk', 'elasticbeanstalk'),
	        array('elastic_load_balancing', 'elasticloadbalancing'),
	        array('elastic_transcoder', 'elastictranscoder'),
	        array('import_export', 'importexport'),
	        array('ops_works', 'opsworks'),
	        array('red_shift', 'redshift'),
	        array('storage_gateway', 'storagegateway'),
	        array('sdb', 'sdb'),
	        array('ec2', 'ec2'),
	        array('s3', 's3')
			);
	}

	/**
	 * 
	 * @dataProvider testGetProperAwsServiceNameProvider
	 */
	public function testGetProperAwsServiceName($input, $expected)
	{
		$output = $this->translator->getProperAwsServiceName($input);
		$this->assertEquals($output, $expected);
	}

	public function testGetProperAwsServiceNameProvider()
	{
		return array(
		    array('autoscaling', 'AutoScaling'),
	        array('cloudformation', 'CloudFormation'),
	        array('cloudfront', 'CloudFront'),
	        array('cloudfront_20120505', 'CloudFront'),
	        array('cloudsearch', 'CloudSearch'),
	        array('cloudtrail', 'CloudTrail'),
	        array('cloudwatch', 'CloudWatch'),
	        array('datapipeline', 'DataPipeline'),
	        array('directconnect', 'DirectConnect'),
	        array('dynamodb', 'DynamoDb'),
	        array('elasticache', 'ElastiCache'),
	        array('elasticbeanstalk', 'ElasticBeanstalk'),
	        array('elasticloadbalancing', 'ElasticLoadBalancing'),
	        array('elastictranscoder', 'ElasticTranscoder'),
	        array('importexport', 'ImportExport'),
	        array('opsworks', 'OpsWorks'),
	        array('redshift', 'Redshift'),
	        array('storagegateway', 'StorageGateway'),
	        array('sdb', 'SimpleDb'),
	        array('ec2', 'Ec2'),
	        array('s3', 'S3'),
	        array('sns', 'Sns')
			);
	}

	/**
	 * 
	 * @dataProvider testGetDefaultAwsClassByServiceNameProvider
	 */
	public function testGetDefaultAwsClassByServiceName($input, $expected)
	{
		$output = $this->translator->getDefaultAwsClassByServiceName($input);
		$this->assertEquals($output, $expected);
	}

	public function testGetDefaultAwsClassByServiceNameProvider()
	{
		return array(
				array('autoscaling', 'Aws\AutoScaling\AutoScalingClient'),
		        array('cloudformation', 'Aws\CloudFormation\CloudFormationClient'),
		        array('cloudfront', 'Aws\CloudFront\CloudFrontClient'),
		        array('cloudfront_20120505', 'Aws\CloudFront\CloudFrontClient'),
		        array('cloudsearch', 'Aws\CloudSearch\CloudSearchClient'),
		        array('cloudtrail', 'Aws\CloudTrail\CloudTrailClient'),
		        array('cloudwatch', 'Aws\CloudWatch\CloudWatchClient'),
		        array('datapipeline', 'Aws\DataPipeline\DataPipelineClient'),
		        array('directconnect', 'Aws\DirectConnect\DirectConnectClient'),
		        array('dynamodb', 'Aws\DynamoDb\DynamoDbClient'),
		        array('elasticache', 'Aws\ElastiCache\ElastiCacheClient'),
		        array('elasticbeanstalk', 'Aws\ElasticBeanstalk\ElasticBeanstalkClient'),
		        array('elasticloadbalancing', 'Aws\ElasticLoadBalancing\ElasticLoadBalancingClient'),
		        array('elastictranscoder', 'Aws\ElasticTranscoder\ElasticTranscoderClient'),
		        array('importexport', 'Aws\ImportExport\ImportExportClient'),
		        array('opsworks', 'Aws\OpsWorks\OpsWorksClient'),
		        array('redshift', 'Aws\Redshift\RedshiftClient'),
		        array('storagegateway', 'Aws\StorageGateway\StorageGatewayClient'),
		        array('sdb', 'Aws\SimpleDb\SimpleDbClient'),
		        array('ec2', 'Aws\Ec2\Ec2Client'),
		        array('s3', 'Aws\S3\S3Client')
			);
	}

	public function testProperServiceNameSet()
	{
		$this->markTestIncomplete();
	}

	public function testProperServiceAliasSet()
	{
		$this->markTestIncomplete();
	}

	public function testProperDefaultServiceExtendsSet()
	{
		$this->markTestIncomplete();
	}

	public function testProperCustomServiceExtendsSet()
	{
		$this->markTestIncomplete();
	}
}
<?php

namespace JLM\AwsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Resource\FileResource;

use JLM\AwsBundle\DependencyInjection\Compiler\S3WrapperCompilerPass;

class JLMAwsBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);

		/**
		 * Since our Extension class relies on the AwsConfigTranslator
		 * class, we need to let the container know about it so that
		 * updates to the translator class will invalidate the container
		 * cache.
		 */
		$container->addResource(new FileResource(__DIR__ . '/Config/AwsConfigTranslator.php'));
	}

	public function boot()
	{
		$container = $this->container;
		if ($container->has('jlm_aws.s3_stream_wrapper_service')) {
			$s3 = $container->get('jlm_aws.s3_stream_wrapper_service');
			$s3->registerStreamWrapper();
		}
	}
}

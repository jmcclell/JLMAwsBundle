<?php

namespace JLM\AwsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Resource\FileResource;

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
}

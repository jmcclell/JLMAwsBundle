<?php 

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
	/**
     * @return array
     */
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new JLM\AwsBundle\JLMAwsBundle()
        );
    }

    /**
     * @return null
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
	{
        $loader->load(__DIR__ . '/Resources/config/framework.yml');

        $env = $this->getEnvironment();
        $pos = strrpos($env, '_');

        if($pos === false) {
            $conf = $env;
        } else {
            $conf = substr_replace($env, '.', $pos, 1);
        }

        $loader->load(__DIR__ . '/Resources/config/config_' . $conf);
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/JLMAwsBundle/cache';
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir() . '/JLMAwsBundle/logs';
    }
}
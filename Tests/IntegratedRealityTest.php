<?php
namespace JLM\AwsBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IntegratedRealityTest extends WebTestCase 
{
	public function testReality()
	{
		$this->assertEquals(1 + 1, 2);
	}
}
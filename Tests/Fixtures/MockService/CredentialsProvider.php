<?php

namespace JLM\AwsBundle\Tests\Fixtures\MockService;

use Aws\Common\Credentials\CredentialsInterface;

class CredentialsProvider implements CredentialsInterface
{
	public function getAccessKeyId()
	{
		return 'MY_PROVIDED_ACCESS_KEY';
	}

	public function getSecretKey()
	{
		return 'MY_PROVIDED_SECRET_KEY';
	}

	public function getSecurityToken()
	{
		return null;
	}

	public function getExpiration()
	{
		return null;
	}

	public function setAccessKeyId($key)
	{
		return;
	}

	public function setSecretKey($secret)
	{
		return;
	}

	public function setSecurityToken($token)
	{
		return;
	}

	public function setExpiration($timestamp)
	{
		return;
	}

	public function isExpired()
	{
		return false;
	}

	public function serialize()
	{
		return array();
	}

	public function unserialize($a)
	{
		return;
	}
}
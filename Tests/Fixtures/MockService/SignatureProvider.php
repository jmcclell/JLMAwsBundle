<?php

namespace JLM\AwsBundle\Tests\Fixtures\MockService;

use Aws\Common\Signature\SignatureInterface;
use Guzzle\Http\Message\RequestInterface;
use Aws\Common\Credentials\CredentialsInterface;

class SignatureProvider implements SignatureInterface
{
	public function signRequest (RequestInterface $request, CredentialsInterface $credentials )
	{
		return $request;
	}
}
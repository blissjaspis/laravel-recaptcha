<?php

namespace BlissJaspis\ReCaptcha\Tests;

use BlissJaspis\ReCaptcha\Facades\ReCaptcha;
use BlissJaspis\ReCaptcha\ReCaptchaServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{

	protected function getPackageAliases($app)
	{
		return [
			'ReCaptcha' => ReCaptcha::class,
		];
	}


	protected function getPackageProviders($app)
	{
		return [ReCaptchaServiceProvider::class];
	}
}

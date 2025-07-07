<?php

namespace BlissJaspis\ReCaptcha\Tests;

use BlissJaspis\ReCaptcha\Facades\ReCaptcha;
use BlissJaspis\ReCaptcha\ReCaptchaBuilderInvisible;
use BlissJaspis\ReCaptcha\ReCaptchaBuilderV2;

class ReCaptchaLangTest extends TestCase
{

	protected $recaptcha_invisible;

	protected $recaptcha_v2;

	public function testHtmlScriptTagJsApiGetHtmlScriptWithHlParam()
	{
		$r = ReCaptcha::htmlScriptTagJsApi();
		$this->assertEquals('<script src="https://www.google.com/recaptcha/api.js?hl=it" async defer></script>', $r);
	}

	public function testHtmlScriptTagJsApiGetHtmlScriptOverridingHlParam()
	{
		$r = ReCaptcha::htmlScriptTagJsApi(['lang' => 'en']);
		$this->assertEquals('<script src="https://www.google.com/recaptcha/api.js?hl=en" async defer></script>', $r);
	}

	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('recaptcha.default_language', 'it');
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this->recaptcha_invisible = new ReCaptchaBuilderInvisible('api_site_key', 'api_secret_key');
		$this->recaptcha_v2 = new ReCaptchaBuilderV2('api_site_key', 'api_secret_key');

	}
}
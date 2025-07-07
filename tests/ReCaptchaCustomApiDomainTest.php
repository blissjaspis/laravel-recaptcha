<?php

namespace BlissJaspis\ReCaptcha\Tests;

use BlissJaspis\ReCaptcha\ReCaptchaBuilderInvisible;
use BlissJaspis\ReCaptcha\ReCaptchaBuilderV2;
use BlissJaspis\ReCaptcha\ReCaptchaBuilderV3;

class ReCaptchaCustomApiDomainTest extends TestCase
{

    protected $recaptcha_invisible;

    protected $recaptcha_v2;

    protected $recaptcha_v3;

    public function testRecaptchaApiDomainChangesByConfig()
    {
        $this->app['config']->set('recaptcha.api_domain', 'www.recaptcha.net');
        $this->assertEquals("www.recaptcha.net", $this->recaptcha_v2->getApiDomain());
        $this->assertEquals("www.recaptcha.net", $this->recaptcha_invisible->getApiDomain());
        $this->assertEquals("www.recaptcha.net", $this->recaptcha_v3->getApiDomain());
    }

    public function testRecaptchaApiDomainChangesByConfigInHtmlScriptTagJsApi()
    {
        $this->assertStringContainsString("https://www.recaptcha.net/recaptcha/api.js", $this->recaptcha_v2->htmlScriptTagJsApi());
        $this->assertStringContainsString("https://www.recaptcha.net/recaptcha/api.js", $this->recaptcha_invisible->htmlScriptTagJsApi());
        $this->assertStringContainsString("https://www.recaptcha.net/recaptcha/api.js", $this->recaptcha_v3->htmlScriptTagJsApi());
    }

    protected function getEnvironmentSetUp($app)
    {

        $app['config']->set('recaptcha.api_domain', 'www.recaptcha.net');
    }

    protected function setUp(): void
    {

        parent::setUp();
        $this->recaptcha_invisible = new ReCaptchaBuilderInvisible('api_site_key', 'api_secret_key');
        $this->recaptcha_v2 = new ReCaptchaBuilderV2('api_site_key', 'api_secret_key');
        $this->recaptcha_v3 = new ReCaptchaBuilderV3('api_site_key', 'api_secret_key');
    }
}

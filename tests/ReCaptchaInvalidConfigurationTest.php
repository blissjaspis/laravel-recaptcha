<?php

namespace BlissJaspis\ReCaptcha\Tests;

use BlissJaspis\ReCaptcha\Exceptions\InvalidConfigurationException;
use BlissJaspis\ReCaptcha\ReCaptchaBuilder;

class ReCaptchaInvalidConfigurationTest extends TestCase
{

    public function testV2HtmlScriptTagJsApiThrowsInvalidConfigurationException()
    {
        $this->expectException(InvalidConfigurationException::class);

        htmlScriptTagJsApi();
    }


    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('recaptcha.api_site_key', 'api_site_key');
        $app['config']->set('recaptcha.api_secret_key', 'api_secret_key');
        $app['config']->set('recaptcha.explicit', true);
        $app['config']->set('recaptcha.tag_attributes.callback', ReCaptchaBuilder::DEFAULT_ONLOAD_JS_FUNCTION);
    }

}
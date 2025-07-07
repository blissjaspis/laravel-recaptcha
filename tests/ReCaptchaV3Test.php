<?php

namespace BlissJaspis\ReCaptcha\Tests;

use BlissJaspis\ReCaptcha\Controllers\ReCaptchaController;
use BlissJaspis\ReCaptcha\Facades\ReCaptcha;
use BlissJaspis\ReCaptcha\ReCaptchaBuilderV3;
use Illuminate\Support\Facades\App;

class ReCaptchaV3Test extends TestCase
{

    protected $recaptcha_v3 = null;

    public function testGetApiVersion()
    {
        $this->assertEquals($this->recaptcha_v3->getVersion(), 'v3');
    }

    public function testAction()
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi(['action' => 'someAction']);
        $this->assertMatchesRegularExpression('/someAction/', $r);
    }

    public function testFetchCallbackFunction()
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi(['callback_then' => 'functionCallbackThen']);
        $this->assertMatchesRegularExpression('/functionCallbackThen\(response\)/', $r);
    }

    public function testcCatchCallbackFunction()
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi(['callback_catch' => 'functionCallbackCatch']);
        $this->assertMatchesRegularExpression('/functionCallbackCatch\(err\)/', $r);
    }

    public function testCustomValidationFunction()
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi(['custom_validation' => 'functionCustomValidation']);
        $this->assertMatchesRegularExpression('/functionCustomValidation\(token\)/', $r);
    }

    public function testCustomValidationUrl()
    {
        $r = $this->recaptcha_v3->htmlScriptTagJsApi();
        $this->assertMatchesRegularExpression('/http:\/\/localhost\/my-custom-url\?my-custom-token-name/', $r);
    }

    public function testValidateController()
    {
        $controller = App::make(ReCaptchaController::class);
        $return = $controller->validateV3();

        $this->assertArrayHasKey("success", $return);
        $this->assertArrayHasKey("error-codes", $return);
    }

    public function testCurlTimeoutIsSet()
    {
        $this->assertEquals($this->recaptcha_v3->getCurlTimeout(), 3);
    }

    public function testHtmlScriptTagJsApiCalledByFacade()
    {
        ReCaptcha::shouldReceive('htmlScriptTagJsApi')
            ->once()
            ->with([]);

        htmlScriptTagJsApi([]);
    }

    public function testValidationUrlShouldBeMyCustomUrl()
    {
        $this->assertEquals($this->recaptcha_v3->getValidationUrl(), "http://localhost/my-custom-url");
    }

    public function testTokenParamNameShouldBeMyCustomTokenParamName()
    {
        $this->assertEquals($this->recaptcha_v3->getTokenParameterName(), "my-custom-token-name");
    }

    public function testValidationUrlShouldBeMyCustomValidationUrl()
    {
        $this->assertEquals($this->recaptcha_v3->getValidationUrlWithToken(), "http://localhost/my-custom-url?my-custom-token-name");
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('recaptcha.version', 'v3');
        $app['config']->set('recaptcha.curl_timeout', 3);

        $app['config']->set('recaptcha.default_validation_route', "my-custom-url");
        $app['config']->set('recaptcha.default_token_parameter_name', "my-custom-token-name");
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->recaptcha_v3 = new ReCaptchaBuilderV3('api_site_key', 'api_secret_key');
    }
}

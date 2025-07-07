<?php

namespace BlissJaspis\ReCaptcha\Tests;

use BlissJaspis\ReCaptcha\Facades\ReCaptcha;
use BlissJaspis\ReCaptcha\ReCaptchaBuilder;
use BlissJaspis\ReCaptcha\ReCaptchaBuilderInvisible;
use BlissJaspis\ReCaptcha\ReCaptchaBuilderV2;
use BlissJaspis\ReCaptcha\ReCaptchaBuilderV3;

class ReCaptchaTest extends TestCase
{

	protected $recaptcha_invisible = null;

	protected $recaptcha_v2 = null;

	protected $recaptcha_v3 = null;

	public function testHtmlScriptTagJsApiGetHtmlScriptTag()
	{
		$r = ReCaptcha::htmlScriptTagJsApi();
		$this->assertEquals('<script src="https://www.google.com/recaptcha/api.js" async defer></script>', $r);
	}

	public function testReCaptchaInvisibleHtmlFormButtonDefault()
	{
		$recaptcha = $this->recaptcha_invisible;
		$html_button = $recaptcha->htmlFormButton();
		$this->assertEquals(
			'<button class="g-recaptcha" data-callback="biscolabLaravelReCaptcha" data-sitekey="api_site_key">Submit</button>',
			$html_button
		);
	}

	public function testReCaptchaInvisibleHtmlFormButtonCustom()
	{
		$recaptcha = $this->recaptcha_invisible;
		$html_button = $recaptcha->htmlFormButton('Custom Text');
		$this->assertEquals(
			'<button class="g-recaptcha" data-callback="biscolabLaravelReCaptcha" data-sitekey="api_site_key">Custom Text</button>',
			$html_button
		);
	}

	public function testReCaptchaV2HtmlFormSnippet()
	{
		$recaptcha = $this->recaptcha_v2;
		$html_snippet = $recaptcha->htmlFormSnippet();
		$this->assertEquals('<div class="g-recaptcha" data-sitekey="api_site_key" data-size="normal" data-theme="light" id="recaptcha-element"></div>', $html_snippet);
	}

	public function testReCaptchaInvisibleHtmlFormSnippetShouldThrowError()
	{
		$this->expectException('\Error');
		$this->recaptcha_invisible->htmlFormSnippet();
	}

	public function testSkipByIpAndReturnArrayReturnsDefaultArray()
	{

		$mock = $this->getMockBuilder(ReCaptchaBuilder::class)
			->setConstructorArgs([
				"api_site_key",
				"api_secret_key"
			])
			->setMethods([
				'returnArray'
			])
			->getMock();

		$mock->method('returnArray')
			->willReturn(true);

		$this->setSkipByIp($this->recaptcha_v3, true);

		$validate = $this->recaptcha_v3->validate("");

		$this->assertEquals([
			"skip_by_ip" => true,
			"score"      => 0.9,
			"success"    => true
		], $validate);
	}

	public function testSkipByIpReturnsValidResponse()
	{
		$this->setSkipByIp($this->recaptcha_invisible, true);
		$validate = $this->recaptcha_invisible->validate("");

		$this->assertTrue($validate);
	}

	public function testDefaultCurlTimeout()
	{
		$this->assertEquals($this->recaptcha_invisible->getCurlTimeout(), ReCaptchaBuilder::DEFAULT_CURL_TIMEOUT);
		$this->assertEquals($this->recaptcha_v2->getCurlTimeout(), ReCaptchaBuilder::DEFAULT_CURL_TIMEOUT);
		$this->assertEquals($this->recaptcha_v3->getCurlTimeout(), ReCaptchaBuilder::DEFAULT_CURL_TIMEOUT);
	}

	public function testReCaptchaV2htmlFormButtonShouldThrowError()
	{
		$this->expectException('\Error');
		$this->recaptcha_v2->htmlFormButton();
	}

	public function testRecaptchaFieldNameHelperReturnsReCaptchaBuilderDefaultFieldName()
	{
		$this->assertEquals(ReCaptchaBuilder::DEFAULT_RECAPTCHA_FIELD_NAME, recaptchaFieldName());
	}

	public function testRecaptchaRuleNameHelperReturnsReCaptchaBuilderDefaultRuleName()
	{
		$this->assertEquals(ReCaptchaBuilder::DEFAULT_RECAPTCHA_RULE_NAME, recaptchaRuleName());
	}

	public function testDefaultRecaptchaApiDomainIsGoogleDotCom()
	{
		$this->assertEquals("www.google.com", $this->recaptcha_v2->getApiDomain());
		$this->assertEquals("www.google.com", $this->recaptcha_invisible->getApiDomain());
		$this->assertEquals("www.google.com", $this->recaptcha_v3->getApiDomain());
	}

	protected function setSkipByIp(ReCaptchaBuilder $builder, bool $value)
	{
		$reflection = new \ReflectionClass($builder);
		$reflection_property = $reflection->getProperty('skip_by_ip');
		$reflection_property->setAccessible(true);
		$reflection_property->setValue($builder, $value);
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this->recaptcha_invisible = new ReCaptchaBuilderInvisible('api_site_key', 'api_secret_key');
		$this->recaptcha_v2 = new ReCaptchaBuilderV2('api_site_key', 'api_secret_key');
		$this->recaptcha_v3 = new ReCaptchaBuilderV3('api_site_key', 'api_secret_key');
	}
}

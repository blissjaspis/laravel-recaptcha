<?php

namespace BlissJaspis\ReCaptcha;

use BlissJaspis\ReCaptcha\Exceptions\InvalidConfigurationException;
use Illuminate\Support\Arr;

class ReCaptchaBuilderV2 extends ReCaptchaBuilder
{

    protected static $allowed_data_attribute = [
        "theme",
        "size",
        "tabindex",
        "callback",
        "expired-callback",
        "error-callback",
    ];

    public function __construct(string $api_site_key, string $api_secret_key)
    {
        parent::__construct($api_site_key, $api_secret_key, 'v2');
    }

    public function htmlFormSnippet(?array $attributes = []): string
    {
        $data_attributes = [];
        $config_data_attributes = array_merge($this->getTagAttributes(), self::cleanAttributes($attributes));
        ksort($config_data_attributes);
        foreach ($config_data_attributes as $k => $v) {
            if ($v) {
                $data_attributes[] = 'data-' . $k . '="' . $v . '"';
            }
        }

        $html = '<div class="g-recaptcha" ' . implode(" ", $data_attributes) . ' id="recaptcha-element"></div>';

        return $html;
    }

    public function getTagAttributes(): array
    {
        $tag_attributes = [
            'sitekey' => $this->api_site_key
        ];

        $tag_attributes = array_merge($tag_attributes, config('recaptcha.tag_attributes', []));

        if (Arr::get($tag_attributes, 'callback') === ReCaptchaBuilder::DEFAULT_ONLOAD_JS_FUNCTION) {
            throw new InvalidConfigurationException('Property "callback" ("data-callback") must be different from "' . ReCaptchaBuilder::DEFAULT_ONLOAD_JS_FUNCTION . '"');
        }

        if (Arr::get($tag_attributes, 'expired-callback') === ReCaptchaBuilder::DEFAULT_ONLOAD_JS_FUNCTION) {
            throw new InvalidConfigurationException('Property "expired-callback" ("data-expired-callback") must be different from "' . ReCaptchaBuilder::DEFAULT_ONLOAD_JS_FUNCTION . '"');
        }

        if (Arr::get($tag_attributes, 'error-callback') === ReCaptchaBuilder::DEFAULT_ONLOAD_JS_FUNCTION) {
            throw new InvalidConfigurationException('Property "error-callback" ("data-error-callback") must be different from "' . ReCaptchaBuilder::DEFAULT_ONLOAD_JS_FUNCTION . '"');
        }

        return $tag_attributes;
    }

    public function getOnLoadCallback(): string
    {
        $attributes = $this->getTagAttributes();

        return "<script>var biscolabOnloadCallback = function() {grecaptcha.render('recaptcha-element', " . json_encode($attributes) . ");};</script>";
    }

    public static function cleanAttributes(?array $attributes = []): array
    {
        return array_filter($attributes, function ($k) {
            return in_array($k, self::$allowed_data_attribute);
        }, ARRAY_FILTER_USE_KEY);
    }
}

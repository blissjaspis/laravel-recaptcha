<?php

namespace BlissJaspis\ReCaptcha;

use BlissJaspis\ReCaptcha\Exceptions\InvalidConfigurationException;
use Illuminate\Support\Arr;

class ReCaptchaBuilderInvisible extends ReCaptchaBuilder
{

    protected $form_id = null;

    public function __construct(string $api_site_key, string $api_secret_key)
    {

        parent::__construct($api_site_key, $api_secret_key, 'invisible');
    }

    public function htmlFormButton($button_label = 'Submit', ?array $properties = []): string
    {

        $tag_properties = '';

        $properties = array_merge([
            'data-callback' => 'biscolabLaravelReCaptcha',
        ], $properties, 
        [
            'data-sitekey'  => $this->api_site_key
        ]);

        if (empty($properties['class'])) {
            $properties['class'] = 'g-recaptcha';
        } else {
            $properties['class'] .= ' g-recaptcha';
        }

        ksort($properties);

        if ($properties) {
            $temp_properties = [];
            foreach ($properties as $k => $v) {
                $temp_properties[] = $k . '="' . $v . '"';
            }

            $tag_properties = implode(" ", $temp_properties);
        }

        return ($this->version == 'invisible') ? '<button ' . $tag_properties . '>' . $button_label . '</button>' : '';
    }

    public function htmlScriptTagJsApi(?array $configuration = []): string
    {

        $html = parent::htmlScriptTagJsApi();

        $form_id = Arr::get($configuration, 'form_id');
        if (!$form_id) {
            $form_id = $this->getFormId();
        } else {
            $this->form_id = $form_id;
        }
        $html .= '<script>
		       function biscolabLaravelReCaptcha(token) {
		         document.getElementById("' . $form_id . '").submit();
		       }
		     </script>';

        return $html;
    }

    public function getFormId(): string
    {

        if (!$this->form_id) {
            $form_id = config('recaptcha.default_form_id');
        } else {
            $form_id = $this->form_id;
        }
        if (!$form_id) {
            throw new InvalidConfigurationException("formId required");
        }

        return $form_id;
    }
}


<?php

namespace BlissJaspis\ReCaptcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string htmlScriptTagJsApi(?array $config = [])
 * @method static string htmlFormButton(?string $button_label = '', ?array $properties = [])
 * @method static string htmlFormSnippet()
 * @method static string getFormId()
 */
class ReCaptcha extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'recaptcha';
    }
}

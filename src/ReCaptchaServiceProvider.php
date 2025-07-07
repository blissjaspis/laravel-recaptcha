<?php

namespace BlissJaspis\ReCaptcha;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ReCaptchaServiceProvider extends ServiceProvider
{

    protected $defer = false;

    public function boot()
    {
        $this->addValidationRule();
        $this->registerRoutes();
        $this->publishes([
            __DIR__ . '/../config/recaptcha.php' => config_path('recaptcha.php'),
        ], 'config');
    }

    public function addValidationRule()
    {
        $message = null;

        if (!config('recaptcha.empty_message')) {
            $message = trans(config('recaptcha.error_message_key'));
        }
        Validator::extendImplicit(recaptchaRuleName(), function ($attribute, $value) {
            return app('recaptcha')->validate($value);
        }, $message);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/recaptcha.php',
            'recaptcha'
        );

        $this->registerReCaptchaBuilder();
    }

    public function provides(): array
    {
        return ['recaptcha'];
    }

    protected function registerRoutes(): ReCaptchaServiceProvider
    {
        Route::get(
            config('recaptcha.default_validation_route', 'blissjaspis-recaptcha/validate'),
            ['uses' => 'BlissJaspis\ReCaptcha\Controllers\ReCaptchaController@validateV3']
        )->middleware('web');

        return $this;
    }

    protected function registerReCaptchaBuilder()
    {
        $this->app->singleton('recaptcha', function ($app) {

            $recaptcha_class = '';

            switch (config('recaptcha.version')) {
                case 'v3':
                    $recaptcha_class = ReCaptchaBuilderV3::class;
                    break;
                case 'v2':
                    $recaptcha_class = ReCaptchaBuilderV2::class;
                    break;
                case 'invisible':
                    $recaptcha_class = ReCaptchaBuilderInvisible::class;
                    break;
            }

            return new $recaptcha_class(config('recaptcha.api_site_key'), config('recaptcha.api_secret_key'));
        });
    }
}

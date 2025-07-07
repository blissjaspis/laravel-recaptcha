# Laravel ReCAPTCHA

**Laravel ReCAPTCHA** is a simple package to embed Google reCAPTCHA in your application.

## What is reCAPTCHA?

Google developers says: "reCAPTCHA protects you against spam and other types of automated abuse. Here, we explain how to add reCAPTCHA to your site or application."

You can find further info at <a href="https://developers.google.com/recaptcha/intro" target="_blank" title="Google reCAPTCHA Developer's Guide">Google reCAPTCHA Developer's Guide</a>

## reCAPTCHA available versions

At this moment there are 3 versions available (for web applications):

- **v3**, the latest (<a href="https://developers.google.com/recaptcha/docs/v3" target="_blank">reCAPTCHA v3</a>)
- **v2 checkbox** or simply reCAPTCHA v2 (<a href="https://developers.google.com/recaptcha/docs/display" target="_blank">reCAPTCHA v2</a>)
- **v2 invisible** (<a href="https://developers.google.com/recaptcha/docs/invisible" target="_blank">Invisible reCAPTCHA</a>)

## Get your key first!

First of all you have to create your own API keys <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a>

Follow the instructions and at the end of the process you will find **Site key** and **Secret key**. Keep them close..you will need soon!

## Requirements

| Dependency | Version |
| :--- | :--- |
| PHP | ^8.1 |
| Laravel | ^10.0, ^11.0, ^12.0 |

## Installation

You can install the package via composer:

```sh
composer require blissjaspis/laravel-recaptcha
```

The service provider will be automatically registered.

You can publish the config file with:
```sh
php artisan vendor:publish --provider="BlissJaspis\ReCaptcha\ReCaptchaServiceProvider"
```

This will create a `config/recaptcha.php` file in your app that you can modify to set your configuration.

### Set your API Keys

Open `.env` file and set `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY`:

```env
# in your .env file
RECAPTCHA_SITE_KEY=<YOUR_API_SITE_KEY>
RECAPTCHA_SECRET_KEY=<YOUR_API_SECRET_KEY>
RECAPTCHA_SKIP_IP=<YOUR_IP_LIST>
```
`RECAPTCHA_SKIP_IP` (not required, CSV format) allows you to add a list of IP/CIDR to whitelist.

### Configuration

Open `config/recaptcha.php` configuration file to set your default `version` and other options. All options are documented inside the configuration file.

> **!!! IMPORTANT !!!** Every time you change your configuration, run `php artisan config:cache` to apply the changes.

## Usage

### Customize error message

For v2 and invisible reCAPTCHA, you can customize the validation error message. Add your message to the `resources/lang/[LANG]/validation.php` file:

```php
return [
    //...
    'recaptcha' => 'The reCAPTCHA validation failed. Please try again.',
];
```

### ReCAPTCHA v2 Checkbox

1.  **Embed in Blade**

    Insert `{!! htmlScriptTagJsApi() !!}` helper before closing `</head>` tag in your template.
    ```blade
    <!DOCTYPE html>
    <html>
        <head>
            ...
            {!! htmlScriptTagJsApi() !!}
        </head>
        ...
    </html>
    ```
    Inside your form, use the `{!! htmlFormSnippet() !!}` helper.

    ```blade
    <form>
        @csrf
        ...
        {!! htmlFormSnippet() !!}
        <button type="submit">Submit</button>
    </form>
    ```

2.  **Verify submitted data**

    Add `recaptcha` to your validation rules:
    ```php
    $validator = Validator::make(request()->all(), [
        'g-recaptcha-response' => 'recaptcha',
        // OR
        recaptchaFieldName() => recaptchaRuleName()
    ]);
    ```

### ReCAPTCHA v2 Invisible

1.  **Embed in Blade**

    Insert `{!! htmlScriptTagJsApi() !!}` before closing `</head>` tag.
    The form requires an ID that matches the one in your `config/recaptcha.php`. By default, it's `blissjaspis-recaptcha-invisible-form`. You can get it with `getFormId()`.
    
    Use the `{!! htmlFormButton('Submit') !!}` helper to generate the submit button.

    ```blade
    <form id="{{ getFormId() }}">
      @csrf
      ...
      {!! htmlFormButton('Submit', ['class' => 'btn btn-primary']) !!}
    </form>
    ```

2.  **Verify submitted data**

    The validation rule is the same as for v2 checkbox.

### ReCAPTCHA v3

1.  **Embed in Blade**

    Insert `{!! htmlScriptTagJsApi() !!}` before closing `</head>` tag. You need to pass some configuration.
    ```blade
    <!DOCTYPE html>
    <html>
        <head>
            ...
            <meta name="csrf-token" content="{{ csrf_token() }}">
            {!! htmlScriptTagJsApi([
                'action' => 'homepage',
                'callback_then' => 'callbackThen',
                'callback_catch' => 'callbackCatch'
            ]) !!}
        </head>
        ...
    </html>
    ```

    The configuration keys are:
    - `action`: The action name for reCAPTCHA v3.
    - `callback_then`: Javascript function to call on success.
    - `callback_catch`: Javascript function to call on error.
    - `custom_validation`: Your own Javascript validation function.

2.  **Handling validation**

    The package provides a built-in Javascript validation system that makes an AJAX call to the validation route. You can define `callback_then` and `callback_catch` functions to handle the response.

    ```html
    <script type="text/javascript">
        function callbackThen(response) {
            // read HTTP status
            console.log(response.status);

            // read Promise object
            response.json().then(function(data){
                console.log(data);
            });
        }
        function callbackCatch(error) {
            console.error('Error:', error)
        }
    </script>
    ```

    Alternatively, you can provide a `custom_validation` function to handle the token yourself.

    ```html
    <script type="text/javascript">
        function myCustomValidation(token) {
            // Your custom validation logic here.
        }
    </script>
    ```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

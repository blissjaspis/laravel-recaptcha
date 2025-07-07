<?php

namespace BlissJaspis\ReCaptcha\Controllers;

use Illuminate\Routing\Controller;

class ReCaptchaController extends Controller
{
	public function validateV3(): array
	{
		$token = request()->input(config('recaptcha.default_token_parameter_name', 'token'), '');

		return recaptcha()->validate($token);
	}
}
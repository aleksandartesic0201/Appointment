<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	// 'stripe' => [
	// 	'model'  => 'App\User',
	// 	'key' => '',
	// 	'secret' => '',
	// ],
	 'stripe' => [
        'secret' => 'sk_test_vnnR4ZU89pS1DCMxzLEqRIu5',
    ],

];

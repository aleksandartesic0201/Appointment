<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware'=>'token'],function(){
	Route::resource('ratings','RatingsController');
	Route::resource('customers','CustomersController');
	/* 2015/6/13 ADDED BY LIU START*/
	Route::post('customersbyfilter','CustomersController@getFilters');
	Route::post('ratingsbyfilter','RatingsController@getFilters');
	Route::post('appointmentsbyfilter','AppointmentsController@getFilters');
	Route::resource('workhours','WorkHoursController', ['only'=>['index', 'show', 'store', 'update']]);
	/* 2015/6/13 ADDED BY LIU END*/
	Route::resource('services','ServicesController');
	Route::resource('appointments','AppointmentsController');
	Route::resource('experts','ExpertsController');
	Route::resource('experts.availability','AvailabilityController');
	Route::resource('countries','CountriesController');
	Route::resource('states','StatesController');
	Route::resource('companies','CompaniesController',['only'=>['index','update']]);
	Route::resource('currencies','CurrenciesController');
	Route::resource('timezones','TimezonesController');
	Route::post('import-customer','CustomersController@importCustomers');
	Route::post('upload-service-image','ServicesController@uploadService');
	Route::post('upload-pic','ExpertsController@uploadPicture');
	Route::post('upload-company-image','CompaniesController@uploadImage');
	Route::resource('dashboard','DashboardController',['only'=>'index']);
	Route::resource('news','NewsController',['only'=>['index','store','show','destroy']]);
	Route::resource('plans','PlansController',['only'=>['index']]);
	Route::resource('subscriptions','SubscriptionsController');
	Route::get('update-card','SubscriptionsController@updateCard');
	//Route::get('editCustomer','CustomersController@update');
});
	Route::get('get-profile-picture','ExpertsController@hasProfilePicture');

	Route::post('auth','Auth\AuthController@postLogin');
	Route::post('forgot-password','Auth\PasswordController@postEmail');
	Route::get('user/{token}','Auth\AuthController@getTokenInfo');
	Route::post('reset','Auth\PasswordController@postReset');
//	Route::get('news/post/{id}','NewsController@post');

	Route::any('auth/twitter', ['as' => 'twitter.login', 'uses'=>'TwittersController@index']);

	Route::any('invoice-paid','SubscriptionsController@paymentDone');
//	Route::get('test',function(){
//		dd(app()->make('auth'));
//		return bcrypt('expert123');
//	});


// Route::get('/test-stripe',function(Stripe $stripe){
// 	$charge = Stripe::charges()->create([
// 					'amount'	=>	300,
// 					'currency'	=>	'USD',
// 					'source'	=>	[
// 										'number'    => '4242424242424242',
// 								        'exp_month' => 6,
// 								        'exp_year'  => 2015,
// 								        'cvc'       => 314,
// 							        ]
// 				]);
	
// });
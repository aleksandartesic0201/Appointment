<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Request;
use Config;
use Validator;
class PasswordController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	use ResetsPasswords;

	/**
	 * Create a new password controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct(Guard $auth, PasswordBroker $passwords)
	{
		$this->auth = $auth;
		$this->passwords = $passwords;

		$this->middleware('guest');
	}

	public function postEmail()
	{
		$email = Request::input('email');
		if( Request::input('type') == 'admin' )
			$this->passwords->setModel('App\Models\Company');
		$response = $this->passwords->sendResetLink(['email'=>$email], function($m)
		{
			$m->subject('Password Reset Link');
		});

		switch ($response)
		{
			case PasswordBroker::RESET_LINK_SENT:
				return response()->json(['success'=>'Your password reset link has been sent to your email address.']);

			case PasswordBroker::INVALID_USER:
				return $this->error("Incorrect email address entered!");
		}
	}

	public function postReset()
	{
		$oValidator = Validator::make(Request::all(),[
			'token' => 'required',
			'email' => 'required',
			'password' => 'required|confirmed',
		]);
		if( $oValidator->fails() )
		{
			$errors = $oValidator->messages()->toArray();
			$messageArray = reset($errors);
			return $this->error(reset($messageArray),500);
		}
		$credentials = Request::only(
			'email', 'password', 'password_confirmation', 'token'
		);
		if( Request::input('type') == 'admin' )
			$this->passwords->setModel('App\Models\Company');
		$response = $this->passwords->reset($credentials, function($user, $password)
		{
			$user->password = bcrypt($password);

			$user->save();
		});

		switch ($response)
		{
			case PasswordBroker::PASSWORD_RESET:
				return response()->json(['password_updated']);

			default:
				return $this->error('We could not locate your email address in our system. Please try again');
		}
	}
}

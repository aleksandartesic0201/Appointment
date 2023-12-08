<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Config;
use Hash;
use App\Models\Expert;
use App\Models\Company;
use App\Models\Token;

use App\Models\Subscription;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

	public function postLogin(Request $reqeust)
	{
		$user = null;
		if( $reqeust->input('type') == 'admin' ){
			$user = Company::where('email',$reqeust->input('email'))->first();
			/* 2015/06/15 ADDED BY LIU START*/
			$id = $user->id;
			/* 2015/06/15 ADDED BY LIU END*/
		}
		else{
			$user = Expert::where('email',$reqeust->input('email'))->with('Permissions')->first();
			/* 2015/06/15 ADDED BY LIU START*/
			$id = $user->company_id;
			/* 2015/06/15 ADDED BY LIU END*/
		}

		if( ! $user OR ! $user->active )
			return $this->error('Invalid credentials',403);


		if( ! Hash::check($reqeust->input('password'),$user->password) ) //add password encryption here
			return $this->error('Invalid credentials',403);
		$token = Token::where('type',$reqeust->input('type') == 'admin' ? 'admin' : 'expert')->where('user_id',$user->id)->first();
		if( ! $token )
		{
			$token = Token::create([
						"user_id"	=>	$user->id,
						"type"	=>	$reqeust->input('type') == 'admin' ? 'admin' : 'expert',
						'token'	=>	str_random(120)
					]);
		}
		$user->token = $token->token;
		/* 2015/06/15 ADDED BY LIU START*/
		$payment = Subscription::where('company_id',$id)->first()->active;
		$user->payment_status = $payment;
		/* 2015/06/15 ADDED BY LIU END*/
		return response()->json($user->toArray());
	}

	public function getTokenInfo($token)
	{
		$token = Token::where('token',$token)->first();
		if( ! $token )
			return $this->error('Invalid Token',403);

		if( $token->type == 'admin' )
			$user = Company::find($token->user_id);
		else
			$user = Expert::with('Permissions')->find($token->user_id);

		$user->token = $token->token;

		return response()->json($user->toArray());
	}

}

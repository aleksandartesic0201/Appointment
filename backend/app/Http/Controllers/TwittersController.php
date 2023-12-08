<?php
	/**
	 * Created by PhpStorm.
	 * User: sagar
	 * Date: 15/5/2015
	 * Time: 6:21 PM
	 */

	namespace app\Http\Controllers;

	use Illuminate\Http\Request;
	use Redirect;
	use Session;
	use Abraham\TwitterOAuth\TwitterOAuth;
	use App\Http\Controllers\Controller;
	use GuzzleHttp;

	define( 'CONSUMER_KEY_TWITTER' , 'MwrYmXJgGCRe1Hct7aCiBiV1g' );
	define( 'CONSUMER_KEY_SECRET_TWITTER' , 'lO9Klz84pV2j8Qjzmt9yLqByocRBM4djCtFgQZhTZ2tzSanKIl' );

	class TwittersController extends Controller
	{

//		public function index ()
//		{
//			$connection = new TwitterOAuth( CONSUMER_KEY_TWITTER , CONSUMER_KEY_SECRET_TWITTER );
//			$request_token = $connection->oauth( "oauth/request_token" , array ( "oauth_callback" => url( 'twitter/callback' ) ) );
//			//callback is set to where the rest of the script is
//
//			//TAKING THE OAUTH TOKEN AND THE TOKEN SECRET AND PUTTING THEM IN COOKIES (NEEDED IN THE NEXT SCRIPT)
//			$oauth_token = $request_token[ 'oauth_token' ];
//			$token_secret = $request_token[ 'oauth_token_secret' ];
//			setcookie( "token_secret" , " " , time() - 3600 );
//			setcookie( "token_secret" , $token_secret , time() + 60 * 10 );
//			setcookie( "oauth_token" , " " , time() - 3600 );
//			setcookie( "oauth_token" , $oauth_token , time() + 60 * 10 );
//
//			//GETTING THE URL FOR ASKING TWITTER TO AUTHORIZE THE APP WITH THE OAUTH TOKEN
//			$url = $connection->url( "oauth/authorize" , array ( "oauth_token" => $oauth_token ) );
//
//			//REDIRECTING TO THE URL
//			return redirect()->to( $url );
//		}
		public function index ( Request $request )
		{
			// Part 1 of 2: Initial request from Satellizer.
			if ( ! $request->input( 'oauth_token' ) || ! $request->input( 'oauth_verifier' ) ) {

				$connection = new TwitterOAuth( CONSUMER_KEY_TWITTER , CONSUMER_KEY_SECRET_TWITTER );
				$request_token = $connection->oauth( "oauth/request_token" , array ( "oauth_callback" => url( 'auth/twitter' ) ) );
				//callback is set to where the rest of the script is
				$oauth_token = $request_token[ 'oauth_token' ];
				$token_secret = $request_token[ 'oauth_token_secret' ];
				setcookie( "token_secret" , " " , time() - 3600 );
				setcookie( "token_secret" , $token_secret , time() + 60 * 10 );
				setcookie( "oauth_token" , " " , time() - 3600 );
				setcookie( "oauth_token" , $oauth_token , time() + 60 * 10 );
				//TAKING THE OAUTH TOKEN AND THE TOKEN SECRET AND PUTTING THEM IN COOKIES (NEEDED IN THE NEXT SCRIPT)

				// Step 2. Send OAuth token back to open the authorization screen.
				$url = $connection->url( "oauth/authorize" , array ( "oauth_token" => $oauth_token ) );

				return redirect()->to( $url );

			}
			// Part 2 of 2: Second request after Authorize app is clicked.
			else {
				$oauth_verifier = $_GET[ 'oauth_verifier' ];
				$token_secret = $_COOKIE[ 'token_secret' ];
				$oauth_token = $_COOKIE[ 'oauth_token' ];

				//EXCHANGING THE TOKENS FOR OAUTH TOKEN AND TOKEN SECRET
				$connection = new TwitterOAuth( CONSUMER_KEY_TWITTER , CONSUMER_KEY_SECRET_TWITTER , $oauth_token , $token_secret );
				$access_token = $connection->oauth( "oauth/access_token" , array ( "oauth_verifier" => $oauth_verifier ) );
				$access_token['type'] = 'twitter';
				//DISPLAY THE TOKENS
				echo "<script>
window.opener.postMessage(" . json_encode( $access_token ) . ",'*');
window.close();
</script>";
				exit();
				//return response()->json($access_token);
			}
		}

		public function callback ()
		{
			$oauth_verifier = $_GET[ 'oauth_verifier' ];
			$token_secret = $_COOKIE[ 'token_secret' ];
			$oauth_token = $_COOKIE[ 'oauth_token' ];

			//EXCHANGING THE TOKENS FOR OAUTH TOKEN AND TOKEN SECRET
			$connection = new TwitterOAuth( CONSUMER_KEY_TWITTER , CONSUMER_KEY_SECRET_TWITTER , $oauth_token , $token_secret );
			$access_token = $connection->oauth( "oauth/access_token" , array ( "oauth_verifier" => $oauth_verifier ) );

			$accessToken = $access_token[ 'oauth_token' ];
			$secretToken = $access_token[ 'oauth_token_secret' ];

			//DISPLAY THE TOKENS
			echo "<b>Access Token : </b>" . $accessToken . "<br />";
			echo "<b>Secret Token : </b>" . $secretToken . "<br />";
		}
	}
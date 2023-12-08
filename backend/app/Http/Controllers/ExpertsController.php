<?php namespace App\Http\Controllers;

use App\Models\Expert;
use App\Models\ExpertMobileSetting;
use App\Models\ExpertPermission;
use App\Models\ExpertDefaultAvailability;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\State;
use Request;
use Auth;
use File;

class ExpertsController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/*  2015/06/10 -DELETED BY LIU START - */
	/*
	public function index ()
	{
		$experts = Expert::with( [
			'Ratings' => function ( $q ) {
				$q->with( 'Customer' );
			} , 'Services'
		] )->where( 'company_id' , Auth::user()->company_id );
		$experts = $experts->get();

	 	return response()->json( $experts->toArray() );
	}*/
	/*  2015/06/10 -DELETED BY LIU START - */

	/*  2015/06/10 -UPDATED BY LIU START - */
	public function index ()
	{
		$experts = Expert::with( [
			'Ratings' => function ( $q ) {
				$q->with( 'Customer' );
			} , 'Services','State'
		] )->where( 'company_id' , Auth::user()->company_id );
		$experts = $experts->get();

	 	return response()->json( $experts->toArray() );
	}
	/*  2015/06/10 -UPDATED BY LIU START - */


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create ()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store ()
	{
		$data = Request::except( [ 'permissions' , 'services' ] );

		if ( ! ( filter_var( $data[ 'email' ] , FILTER_VALIDATE_EMAIL )
			&& preg_match( '/@.+\./' , $data[ 'email' ] ) )
		)
			return $this->error( 'please insert valid email' , 500 );

		$expert = Expert::where( 'email' , $data[ 'email' ] )->first();
		if ( $expert )
			return $this->error( 'This email is already exists' );

		$currentPackage = Subscription::where('company_id',Auth::user()->company_id)->first();
		if( ! $currentPackage )
			return $this->error('We can\'t find your subscription with us please contact Administrator',500 );

		$currentPackage = Package::find($currentPackage->package_id);
		if( ! $currentPackage )
			return $this->error('The subscription package you have selected is not valid for now please contact Administrator',500);

		$totalExperts = Expert::where('company_id',Auth::user()->company_id)->where('active',1)->count();
		if( $totalExperts >= $currentPackage->no_of_expert )
			return $this->error('Your current package only allows you to add '.$currentPackage->no_of_expert.' number of active team member. In order to add more, please upgrade your package from setting section -> Basic - > Billing section.',500);

		$data[ 'password' ] = bcrypt( $data[ 'password' ] );

		$expert = Expert::create( $data );

		$permission = Request::input( 'permissions' );
		$permission[ 'expert_id' ] = $expert->id;

		$expertPermission = ExpertPermission::where( 'expert_id' , $expert->id )->first();
		if ( $expertPermission )
			ExpertPermission::where( 'expert_id' , $expert->id )->update( $permission );
		else
			ExpertPermission::create( $permission );

		$services = Request::input( 'services' );
		$servicesArray = [ ];
		foreach ( $services AS $service )
			$servicesArray[ ] = $service[ 'id' ];

		$expert->Services()->sync( $servicesArray );
		$availabilityData = array (
			'expert_id'         => $expert->id ,
			'working'           => 1 ,
			'morning_working'   => 1 ,
			'afternoon_working' => 1 ,
			'evening_working'   => 1 ,
			'morning_start'     => '00:00:00' ,
			'morning_end'       => '12:00:00' ,
			'afternoon_start'   => '12:00:00' ,
			'afternoon_end'     => '18:00:00' ,
			'evening_start'     => '18:00:00' ,
			'evening_end'       => '23:59:00'
		);
		$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
		foreach($days AS $day)
		{
			$availabilityData['day'] = $day;
			ExpertDefaultAvailability::create($availabilityData);
		}
		ExpertMobileSetting::create(['expert_id'=>$expert->id]);
		return $this->show( $expert->id );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show ( $id )
	{
		$expert = Expert::with( [ 'Permissions' ] )->find( $id );
		if ( ! $expert )
			return $this->error( 'expert not found' );

		return response()->json( $expert );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit ( $id )
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function update ( $id )
	{
		$data = Request::except( [ 'permissions' , 'profile_photo' , 'services','availability' ] );

		if ( Request::has( 'password' ) && ! empty( $data[ 'password' ] ) )
			$data[ 'password' ] = bcrypt( $data[ 'password' ] );
		if( ! Request::has('availability'))
		{
			$currentPackage = Subscription::where('company_id',Auth::user()->company_id)->first();
			if( ! $currentPackage )
				return $this->error('We can\'t find your subscription with us please contact Administrator',500 );

			$currentPackage = Package::find($currentPackage->package_id);
			if( ! $currentPackage )
				return $this->error('The subscription package you have selected is not valid for now please contact Administrator',500);

			$totalExperts = Expert::where('company_id',Auth::user()->company_id)->where('active',1)->where('id','!=',$id)->count();
			if( $totalExperts >= $currentPackage->no_of_expert && $data['active'] == 1 )
				return $this->error('Your current package only allows you to add '.$currentPackage->no_of_expert.' number of active team member. In order to add more, please upgrade your package from setting section -> Basic - > Billing section.',500);
		}

		Expert::where( 'id' , $id )->update( $data );

		$permission = Request::input( 'permissions' );
		$permission[ 'expert_id' ] = $id;

		$expertPermission = ExpertPermission::where( 'expert_id' , $id )->first();
		if ( $expertPermission )
			ExpertPermission::where( 'expert_id' , $id )->update( $permission );
		else
			ExpertPermission::create( $permission );

		if ( Request::has( 'services' ) ) {
			$services = Request::input( 'services' );
			$servicesArray = [ ];
			foreach ( $services AS $service )
				$servicesArray[ ] = $service[ 'id' ];

			$expert = Expert::find( $id );

			$expert->Services()->sync( $servicesArray );
		}

		return $this->show( $id );

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy ( $id )
	{
		//
	}

	public function uploadPicture ()
	{
		$id = Request::input( 'expert_id' );
		$string = Request::input( 'img' );

		list( $type , $data ) = explode( ';' , $string );
		list( , $data ) = explode( ',' , $string );
		$data = base64_decode( $data );

		file_put_contents( public_path( 'profile/' . $id . ".png" ) , $data );

		$expert = Expert::find( $id );
		$expert->profile_photo = url( 'profile/' . $id . ".png" );
		$expert->save();

		return response()->json( $expert->toArray() );
	}

	public function hasProfilePicture ()
	{
		$expert_id = Request::input( 'expert_id' , 0 );
		$path = file_exists( public_path( 'profile/' . $expert_id . ".png" ) ) ? public_path( 'profile/' . $expert_id . ".png" ) : public_path( 'profile/user.png' );

		return File::get( $path );
	}
}

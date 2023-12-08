<?php namespace App\Http\Controllers;
use App\Models\ExpertDefaultAvailability;
use Request;
use Carbon\Carbon;
use App\Models\Expert;
use App\Models\ExpertAvailabilityDate;
class AvailabilityController extends Controller {
	public function index($id)
	{
		$expert = Expert::find($id);
		$expertAvailabilityOnDays = array();
		$aDefaultAvailability = $expert->DefaultAvailability()->get()->toArray();
		foreach( $aDefaultAvailability AS $array )
			$expertAvailabilityOnDays[$array['day']] = $array;

		if( Request::has('date') )
		{
			$aDateAvailability = $expert->DateAvailability()->date(Request::input('date'))->first();
			if( $aDateAvailability )
				return response()->json([$aDateAvailability->toArray()]);
			else
			{
				$day = Carbon::createFromFormat('Y-m-d',Request::input('date'))->format('l');
				return response()->json([$expertAvailabilityOnDays[$day]]);
			}
		}

		return response()->json($aDefaultAvailability);
	}

	public function store($id)
	{
		$data = Request::all();
		$expert = Expert::find($id);
		if( Request::has('ondate') ) {
			$aDateAvailability = $expert->DateAvailability()->date( Request::input( 'ondate' ) )->first();
			if ( $aDateAvailability ) {
				$data[ 'id' ] = $aDateAvailability->id;
				ExpertAvailabilityDate::where( 'id' , $data[ 'id' ] )->update( $data );
				$response = $data;
			}
			else {
				$response = ExpertAvailabilityDate::create( $data );
				$response = $response->toArray();
			}
		}
		else
		{
			$aDefaultAvailability = $expert->DefaultAvailability()->day(Request::input('day'))->first();
			if( $aDefaultAvailability )
			{
				$data['id'] = $aDefaultAvailability->id;
				ExpertDefaultAvailability::where('id',$data['id'])->update($data);
				$response = $data;
			}
			else {
				$response = ExpertDefaultAvailability::create($data);
				$response = $response->toArray();
			}
		}
		return response()->json($response);
	}

	public function update($id)
	{

	}

	public function show($id)
	{

	}
}
<?php namespace App\Http\Controllers;

use App\Models\FooterLink;
use App\Models\HeaderLink;
use Auth;
use App\Http\Requests;
use Request;
use App\Models\WorkHours;

class WorkHoursController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index ()
	{
		$workhours = WorkHours::where('company', Auth::user()->company_id)->first();			
		return response()->json($workhours);						
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return response()->json(WorkHours::find($id)->toArray());
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
		$workhours = WorkHours::find( $id );
		$aData = Request::except( [ 'id' ] );

		if ( ! $workhours )
			return $this->error( "Unable to find work hours" , 404 );

		WorkHours::where( 'id' , $id )->update( $aData );

		return $this->index();
	}

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */

	public function store(Request $request)
	{		
		$data = Request::except(['']);
		$workhours = WorkHours::where('company', Auth::user()->company_id)->first();	
		
		if ($workhours == null) {
			$data['company'] = Auth::user()->company_id;
			$workhours = WorkHours::create($data);	
		}
	
		return $this->show($workhours->id);
		//return response()->json(WorkHours::all()->toArray());		
	}

}

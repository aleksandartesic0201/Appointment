<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Expert;

use App\Models\Appointment;
use Auth;
class ServicesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		
		/*  2015/06/10 -DELETED BY LIU START - */
		//$services = Service::where('company_id',Auth::user()->company_id)->get();
		/*  2015/06/10 -DELETED BY LIU START - */
		$services = Service::where('company_id',Auth::user()->company_id)->with(['Appointments'])->get();
		if( $request->has('expert_id') )
		{
			$expert = Expert::find($request->input('expert_id'));
			$services = $expert->Services()->get();
		}
		/*  2015/06/10 -ADDED BY LIU START - */
        $services->each(function ($q) {
            $c = $q->Appointments;
            $q->total_appointments = $c->count();
        });
        /*  2015/06/10 -ADDED BY LIU START - */
		return response()->json($services);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$data = $request->all();
		$service = Service::create($data);

		return $this->show($service->id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		/*  2015/06/11 -DELETED BY LIU START - */
		//return response()->json(Service::find($id)->toArray());
		/*  2015/06/11 -DELETED BY LIU END - */
		/*  2015/06/11 -ADDED BY LIU START - */
	    $services = Service::where('id',$id)->with(['Appointments'])->get();
        $services->each(function ($q) {
            $c = $q->Appointments;
            $q->total_appointments = $c->count();
        });
		return response()->json($services[0]->toArray());
		 /*  2015/06/11 -ADDED BY LIU END - */
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id,Request $request)
	{
		Service::where('id',$id)->update($request->all());
		return $this->show($id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function uploadService(Request $request)
	{
		$id = $request->input('service_id');
		$image_type = $request->input('type');
		$string = $request->input('img');

		list($type, $data) = explode(';', $string);
		list(, $data)      = explode(',', $string);
		$data = base64_decode($data);

		$name = 'services_images/';
		if( $image_type == 'icon' )
			$name .= $id.".png";
		else
			$name .= $id."_".$image_type.".png";
		file_put_contents(public_path($name), $data);

		$service = Service::find($id);
		if( $image_type == 'icon' )
			$service->icon_location = url($name);
		else
		{
			$images = json_decode($service->images,true);
			if( empty( $images ) )
			{
				$images = array();
			}
			$images[$image_type] = url($name);
			$service->images = json_encode($images);
		}
		$service->save();

		return response()->json($service->toArray());
	}

}

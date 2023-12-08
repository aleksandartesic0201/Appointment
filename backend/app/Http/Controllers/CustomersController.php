<?php namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Country;

use App\Models\State;
use App\Models\Appointment;
use App\Models\Company;
use App\Models\AppointmentTransaction;
use App\Models\Expert;
use App\Models\Rating;
use Config;
use Auth;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request,Customer $customer)
	{

		$customers = Customer::whereHas('Companies',function($q){
			$q->where('company_id',Auth::user()->company_id);
		});
		if( $request->has('datatables') )
		{
			$customers = $customers->where('active',1)->with(['Appointments','Country','State']);
			$term = $request->input('search');
			if ( !empty($term['value']) ) {
				$customers->where( function ( $q ) use ( $term ) {
					$q->where( 'name' , 'LIKE' , '%' . $term['value'] . "%" );
					$q->orWhere( 'last_name' , 'LIKE' , '%' . $term['value'] . "%" );
					$q->orWhere( 'email' , 'LIKE' , '%' . $term['value'] . "%" );
					$q->orWhere( 'address' , 'LIKE' , '%' . $term['value'] . "%" );
					$q->orWhere( 'contact_number' , 'LIKE' , '%' . $term['value'] . "%" );
				} )->where(function($q){
					$q->whereNotNull( 'name' );
					$q->whereNotNull( 'last_name' );
					$q->whereNotNull( 'email' );
					$q->whereNotNull( 'address' );
					$q->whereNotNull( 'contact_number' );
				});
			}
			/*  2015/06/13 ADDED BY LIU START */
			if($request->has('filter_spent')){
				$new = array();
				$spent = $request->input('filter_spent');
				$filters = DB::table('company_customers')
						->leftJoin('company_appointments','company_appointments.customer_id','=','company_customers.id')
						->leftJoin('company_appointment_transaction','company_appointment_transaction.appointment_id','=','company_appointments.id')
						->select('company_customers.id',DB::raw('SUM("company_appointment_transaction.amount") as all_amount'))
						->groupBy('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					if($filter->all_amount >= $spent)
						array_push($new,$filter->id);
				}
				$customers = $customers->whereIn('id', $new);
			}
			if($request->has('filter_days')){
	        	$new = array();
	        	$days = $request->input('filter_days');
	            $date = date("Y-m-d H:i:s", strtotime("-".$days." days"));
	            $filters = DB::table('company_customers')
						->leftJoin('company_appointments','company_appointments.customer_id','=','company_customers.id')
						->where('company_appointments.created','>',$date)
						->select('company_customers.id')
						->groupBy('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					array_push($new,$filter->id);
				}
				$customers = $customers->whereNotIn('id', $new);
	        }
			if($request->has('filter_expert_id')){
				$new = array();
				$experts = $request->input('filter_expert_id');
				$filters = DB::table('company_customers')
						->leftJoin('company_appointments','company_appointments.customer_id','=','company_customers.id')
						->whereIn('company_appointments.expert_id',$experts)
						->select('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					array_push($new,$filter->id);
				}
				$customers = $customers->whereIn('id', $new);
			}
			if($request->has('filter_ratings')){
				$new = array();
				$ratings = $request->input('filter_ratings');
				$filters = DB::table('company_customers')
						->leftJoin('company_expert_ratings','company_expert_ratings.rated_by','=','company_customers.id')
						->select(array('company_customers.id',DB::raw('COUNT(company_expert_ratings.rated_by) as all_ratings')))
						->groupBy('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					if($filter->all_ratings >= $ratings)
						array_push($new,$filter->id);
				}
				$customers = $customers->whereIn('id', $new);
			}
			if($request->has('filter_stars')){
				$new = array();				
				$stars = $request->input('filter_stars');
				$filters = DB::table('company_customers')
						->leftJoin('company_expert_ratings','company_expert_ratings.rated_by','=','company_customers.id')
						->select(array('company_customers.id',DB::raw('MAX(company_expert_ratings.rating) as all_stars')))
						->groupBy('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					if($filter->all_stars==null)
						$filter->all_stars=0;
					if($filter->all_stars >= $stars)
						array_push($new,$filter->id);
				}
				$customers = $customers->whereIn('id', $new);
			}			 
			$order = $request->input('order');
			$order = reset($order);
			$columns = $request->input('columns');
			if($columns[$order['column']]['data'] != 'total_spent')
				$customers = $customers->orderBy($columns[$order['column']]['data'],$order['dir']);
			/* 2015/06/12 -UPDATED BY LIU END- */
			//$customer = $customer->skip($request->input('start'))->take($request->input('length'));
			$customers = $customers->paginate($request->length);
			$customers->each(function($q){
				$a = $q->Appointments()->with(['Transactions','Expert'])->get();
				$q->total_appointments = $a->count();
				$q->cancelled_appointments = $a->filter(function($x){
					return $x->cancel_time != '0000-00-00 00:00:00';
				})->count();
				$total = 0;
				$appointments = $a->toArray();
				$experts = array();
				$date = "";
				foreach( $appointments AS $appointment )
				{
					foreach( $appointment['transactions'] as $t){
						$total += $t['amount'];
					}
					$experts[] = $appointment['expert']['firstname'];
					if($appointment['created'] > $date)
						$date = $appointment['created'];
				}
				$q->last_date = $date;
				$q->worked_experts = implode(',',array_unique($experts) );
				$q->total_spent = $total;
				if($q->country_id!=0)
					$q->country = $q->Country->country_name;
				else
					$q->country = "";
				if($q->state_id!=0)
					$q->state = $q->State->state_name;
				else
					$q->state = "";
				unset($q->Country);
				unset($q->State);
				unset($q->Appointments);
				unset($q);
			});
		}
		else
		{
			if( $request->has('email') )
				$customers = $customers->where('email','LIKE',"%".$request->input('email')."%")->get();
			else
				$customers = $customers->with(['Country','State'])->get();
		}
		return response()->json($customers);
	}

	public function getFilters(Request $request,Customer $customer) {
		$customers = Customer::whereHas('Companies',function($q){
			$q->where('company_id',Auth::user()->company_id);
		});
		if( $request->has('datatables') )
		{
			$customers = $customers->where('active',1)->with(['Appointments','Country','State']);
			$term = $request->input('search');
			if ( !empty($term['value']) ) {
				$customers->where( function ( $q ) use ( $term ) {
					$q->where( 'name' , 'LIKE' , '%' . $term['value'] . "%" );
					$q->orWhere( 'last_name' , 'LIKE' , '%' . $term['value'] . "%" );
					$q->orWhere( 'email' , 'LIKE' , '%' . $term['value'] . "%" );
					$q->orWhere( 'address' , 'LIKE' , '%' . $term['value'] . "%" );
					$q->orWhere( 'contact_number' , 'LIKE' , '%' . $term['value'] . "%" );
				} )->where(function($q){
					$q->whereNotNull( 'name' );
					$q->whereNotNull( 'last_name' );
					$q->whereNotNull( 'email' );
					$q->whereNotNull( 'address' );
					$q->whereNotNull( 'contact_number' );
				});
			}
			/*  2015/06/13 ADDED BY LIU START */
			if($request->has('filter_spent')){
				$new = array();
				$spent = $request->input('filter_spent');
				$filters = DB::table('company_customers')
						->leftJoin('company_appointments','company_appointments.customer_id','=','company_customers.id')
						->leftJoin('company_appointment_transaction','company_appointment_transaction.appointment_id','=','company_appointments.id')
						->select('company_customers.id',DB::raw('SUM("company_appointment_transaction.amount") as all_amount'))
						->groupBy('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					if($filter->all_amount >= $spent)
						array_push($new,$filter->id);
				}
				$customers = $customers->whereIn('id', $new);
			}
			if($request->has('filter_days')){
	        	$new = array();
	        	$days = $request->input('filter_days');
	            $date = date("Y-m-d H:i:s", strtotime("-".$days." days"));
	            $filters = DB::table('company_customers')
						->leftJoin('company_appointments','company_appointments.customer_id','=','company_customers.id')
						->where('company_appointments.created','>',$date)
						->select('company_customers.id')
						->groupBy('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					array_push($new,$filter->id);
				}
				$customers = $customers->whereNotIn('id', $new);
	        }
			if($request->has('filter_expert_id')){
				$new = array();
				$experts = $request->input('filter_expert_id');
				$filters = DB::table('company_customers')
						->leftJoin('company_appointments','company_appointments.customer_id','=','company_customers.id')
						->whereIn('company_appointments.expert_id',$experts)
						->select('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					array_push($new,$filter->id);
				}
				$customers = $customers->whereIn('id', $new);
			}
			if($request->has('filter_ratings')){
				$new = array();
				$ratings = $request->input('filter_ratings');
				$filters = DB::table('company_customers')
						->leftJoin('company_expert_ratings','company_expert_ratings.rated_by','=','company_customers.id')
						->select(array('company_customers.id',DB::raw('COUNT(company_expert_ratings.rated_by) as all_ratings')))
						->groupBy('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					if($filter->all_ratings >= $ratings)
						array_push($new,$filter->id);
				}
				$customers = $customers->whereIn('id', $new);
			}
			if($request->has('filter_stars')){
				$new = array();				
				$stars = $request->input('filter_stars');
				$filters = DB::table('company_customers')
						->leftJoin('company_expert_ratings','company_expert_ratings.rated_by','=','company_customers.id')
						->select(array('company_customers.id',DB::raw('MAX(company_expert_ratings.rating) as all_stars')))
						->groupBy('company_customers.id')
						->get();
				foreach ($filters as $filter) {
					if($filter->all_stars==null)
						$filter->all_stars=0;
					if($filter->all_stars >= $stars)
						array_push($new,$filter->id);
				}
				$customers = $customers->whereIn('id', $new);
			}
			$order = $request->input('order');
			$order = reset($order);
			$columns = $request->input('columns');
			if($columns[$order['column']]['data'] != 'total_spent')
				$customers = $customers->orderBy($columns[$order['column']]['data'],$order['dir']);
			/* 2015/06/12 -ADDED BY LIU END- */

			//$customer = $customer->skip($request->input('start'))->take($request->input('length'));
			$customers = $customers->paginate($request->length);
			$customers->each(function($q){
				$a = $q->Appointments()->with(['Transactions','Expert'])->get();
				$q->total_appointments = $a->count();
				$q->cancelled_appointments = $a->filter(function($x){
					return $x->cancel_time != '0000-00-00 00:00:00';
				})->count();
				$total = 0;
				$appointments = $a->toArray();
				$experts = array();
				/* 2015/06/14 UPDATED BY LIU START */
				$date = "";
				foreach( $appointments AS $appointment )
				{
					foreach( $appointment['transactions'] as $t){
						$total += $t['amount'];
					}
					$experts[] = $appointment['expert']['firstname'];
					if($appointment['created'] > $date)
						$date = $appointment['created'];
				}
				$q->last_date = $date;
				/* 2015/06/14 UPDATED BY LIU END */
				$q->worked_experts = implode(',',array_unique($experts) );
				$q->total_spent = $total;
				if($q->country_id!=0)
					$q->country = $q->Country->country_name;
				else
					$q->country = "";
				if($q->state_id!=0)
					$q->state = $q->State->state_name;
				else
					$q->state = "";
				unset($q->Country);
				unset($q->State);
				unset($q->Appointments);
				unset($q);
			});
		}
		else
		{
			if( $request->has('email') )
				$customers = $customers->where('email','LIKE',"%".$request->input('email')."%")->get();
			else
				$customers = $customers->with(['Country','State'])->get();
		}
		return response()->json($customers);
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
		//
		$data = $request->input('draw');
		return "Test";
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
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
	public function update(Request $request, $id)
	{
		/* 2015/06/15 ADDED BY LIU START */
		$customer = Customer::find( $id );
		if ( ! $customer )
			return $this->error( "Unable to find customer" , 404 );
		$country = $request->input('country');
		$state = $request->input('state');
		$countryid = Country::where('country_name',$country)->first()->id;
		$stateid = State::where('state_name',$state)->first()->id;
		$customerdata['name'] = $request->input('firstname');
		$customerdata['last_name'] = $request->input('lastname');
		$customerdata['email'] = $request->input('email');
		$customerdata['country_id'] = $countryid;
		$customerdata['state_id'] = $stateid;
		$customerdata['city'] = $request->input('city');
		$customerdata['contact_number'] = $request->input('phonenumber');
		$customerdata['address'] = $request->input('address');
		$customerdata['suite'] = $request->input('suite');
		$customerdata['zipcode'] = $request->input('zipcode');
		Customer::find($id)->update( $customerdata );
		return $id;
		/* 2015/06/15 ADDED BY LIU END */
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


	public function importCustomers(Request $request)
	{
		if( ! $request->hasFile('file'))
			return $this->error('file not selected!');


		$file = $request->file('file');
		$ext = $file->getClientOriginalExtension();
		$filename = time().".".$file->getClientOriginalExtension();
		$file->move(public_path(),$filename);
		if( $ext == 'csv' )
		{
			$csvString = file_get_contents(public_path("/").$filename);
			$response = $this->processCSV($csvString);
		}


		@unlink(public_path("/").$filename);

		return response()->json($response);
	}

	private function processCSV($string = '')
	{
		$response = array();
		$response['totalRecords'] = 0;
		$response['importedRecords'] = 0;
		$response['failedRecords'] = 0;
		$response['existedRecords'] = 0;

		$rows = explode("\r\n",$string);
		unset($rows[0]);
		foreach( $rows AS $row )
		{
			if( ! empty( $row ) ) {
				$response['totalRecords']++;
				$columns = explode( "," , $row );
				$data = array(
					'name'  =>  $columns[0],
					'last_name' =>  $columns[1],
					'address'   =>  $columns[2],
					'suite' =>  $columns[3],
					'city'  =>  $columns[4],
					'state_id' => $columns[5],
					'country_id'    =>  $columns[6],
					'zipcode'   =>  $columns[7],
					'contact_number'    =>  $columns[8],
					'email'     =>  $columns[9],
				);
				/* 2015/06/11 UPDATED BY LIU START */
				$email = Customer::where('email', $data['email'])->first();
				if( $email )
				{
					Customer::where('email', $data['email'])->update(array('name' => $data['name'],
																	'last_name' => $data['last_name'], 
																	'address' => $data['address'],
																	'suite' => $data['suite'],
										            				'city' => $data['city'], 	
										            				'state_id' => $data['state_id'], 
										            				'country_id' => $data['country_id'], 
										            				'zipcode' => $data['zipcode'],
										            				'contact_number' => $data['contact_number'], 
										            				'email' => $data['email']));

					$response['existedRecords']++;
					continue;
				}else{
					$customer = new Customer;
					$customer->name = $data['name'];
					$customer->last_name = $data['last_name'];
					$customer->address = $data['address'];
					$customer->suite = $data['suite'];
					$customer->city = $data['city'];
					$customer->state_id = $data['state_id'];
					$customer->country_id = $data['country_id'];
					$customer->zipcode = $data['zipcode'];
					$customer->contact_number = $data['contact_number'];
					$customer->email = $data['email'];
					$customer->save();
				}

				/* 2015/06/11 UPDATED BY LIU START */
				$country = Country::where('country_name',$data['country_id'])->first();
				if( ! $country )
				{
					$response['failedRecords']++;
					continue;
				}
				$state = State::where('state_name',$data['state_id'])->first();
				if( ! $state )
				{
					$response['failedRecords']++;
					continue;
				}
				/* 2015/06/11 DELETED BY LIU START */
				/*
				$email = Customer::where('email',$data['email'])->whereHas('Companies',function($q){
					$q->where('company_id',Auth::user()->company_id);
				})->first();
				if( $email )
				{
					$response['existedRecords']++;
					continue;
				}
				*/
				/* 2015/06/11 DELETED BY LIU END */
				if( !(filter_var($data['email'], FILTER_VALIDATE_EMAIL)
					&& preg_match('/@.+\./', $data['email'])) )
				{
					$response['failedRecords']++;
					continue;
				}
				$data['state_id'] = $state->id;
				$data['country_id'] = $country->id;
				$customer = Customer::create($data);
				if( $customer )
				{
					$response['importedRecords']++;
					$companyCustomerData = array('company_id'=>Auth::user()->company_id,'customer_id'=>$customer->id);
					DB::table('company_customer_relation')->insert($companyCustomerData);
				}
			}
		}

		return $response;
	}

}

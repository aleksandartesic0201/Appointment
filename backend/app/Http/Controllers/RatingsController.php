<?php namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Rating;

use App\Models\Customer;
use App\Models\Expert;

use Auth;

class RatingsController extends Controller{

	public function index(Request $request)
	{
		if( $request->has('datatables') )
		{
			$term = $request->input('search');
			$ratings = Rating::whereHas('Expert',function($q){
				$q->where('company_id',Auth::user()->company_id);
			});
			/*  2015/06/10 -ADDED BY LIU START - */
			$ratings = Rating::whereHas('Customer',function($q){
				$q->where('active','<',"2");
			});
			/*  2015/06/10 -ADDED BY LIU START - */
			if( ! empty( $term['value'] ) )
			{
				$ratings = $ratings->whereHas('Customer',function($q) use($term){
					$q->where('name','LIKE','%'.$term['value']."%");
				})->orWhereHas('Expert',function($q) use($term){
					$q->where('firstname','LIKE','%'.$term['value']."%");
				});
			}
			$order = $request->input('order');
			$order = reset($order);
			$columns = $request->input('columns');
			//$ratings = $ratings->orderBy($columns[$order['column']]['data'],$order['dir']);
			if( $columns[$order['column']]['data'] != 'expert_name' && $columns[$order['column']]['data'] != 'customer_name' )
			{
				$ratings = $ratings->orderBy($columns[$order['column']]['data'],$order['dir']);
			}
			if( $request->input('expert_id') )
				$ratings = $ratings->where('expert_id',$request->input('expert_id'));
			//$customer = $customer->skip($request->input('start'))->take($request->input('length'));
			$ratings = $ratings->paginate($request->length);
			$ratings->each(function($q){

				$q->customer_name = $q->Customer->name;
				$q->customer_email = $q->Customer->email;
				$q->expert_name = $q->Expert->firstname." ".$q->Expert->lastname;
				$q->customer_phone = $q->Customer->contact_number;
				$q->customer_address = $q->Customer->address;
				$q->customer_city = $q->Customer->city;
				$q->customer_state = $q->Customer->State()->first()->state_name;
				$q->customer_zipcode = $q->Customer->zipcode;
				$q->rating = $q->rating;
				$q->comment = $q->comment;
				$q->created = $q->created;
				$q->active = $q->active;
				unset($q->Customer);
				unset($q->Expert);
			});
		}
		else
		{
			$ratings = Rating::all();
		}
		return response()->json($ratings);
	}

	public function getFilters(Request $request)
	{
		if( $request->has('datatables') )
		{
			$term = $request->input('search');
			$ratings = Rating::whereHas('Expert',function($q){
				$q->where('company_id',Auth::user()->company_id);
			});
			/*  2015/06/10 -ADDED BY LIU START - */
			$ratings = Rating::whereHas('Customer',function($q){
				$q->where('active','<',"2");
			});
			/*  2015/06/10 -ADDED BY LIU START - */
			if( ! empty( $term['value'] ) )
			{
				$ratings = $ratings->whereHas('Customer',function($q) use($term){
					$q->where('name','LIKE','%'.$term['value']."%");
				})->orWhereHas('Expert',function($q) use($term){
					$q->where('firstname','LIKE','%'.$term['value']."%");
				});
			}
			$order = $request->input('order');
			$order = reset($order);
			$columns = $request->input('columns');
			//$ratings = $ratings->orderBy($columns[$order['column']]['data'],$order['dir']);
			if( $columns[$order['column']]['data'] != 'expert_name' && $columns[$order['column']]['data'] != 'customer_name' )
			{
				$ratings = $ratings->orderBy($columns[$order['column']]['data'],$order['dir']);
			}
			if( $request->input('expert_id') )
				$ratings = $ratings->where('expert_id',$request->input('expert_id'));
			//$customer = $customer->skip($request->input('start'))->take($request->input('length'));
			$ratings = $ratings->paginate($request->length);
			$ratings->each(function($q){

				$q->customer_name = $q->Customer->name;
				$q->customer_email = $q->Customer->email;
				$q->expert_name = $q->Expert->firstname." ".$q->Expert->lastname;
				$q->customer_phone = $q->Customer->contact_number;
				$q->customer_address = $q->Customer->address;
				$q->customer_city = $q->Customer->city;
				$q->customer_state = $q->Customer->State()->first()->state_name;
				$q->customer_zipcode = $q->Customer->zipcode;
				$q->rating = $q->rating;
				$q->comment = $q->comment;
				$q->created = $q->created;
				$q->active = $q->active;
				unset($q->Customer);
				unset($q->Expert);
			});
		}
		else
		{
			$ratings = Rating::all();
		}
		return response()->json($ratings);
	}
	
	public function update(Request $request,$id)
	{
		$active = $request->input('active');
		Rating::where('id',$id)->update(['active'=>$active]);
		return response()->json(['success'=>true]);
	}
}
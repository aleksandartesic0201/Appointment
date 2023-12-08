<?php namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Expert;
use App\Models\Appointment;
use App\Models\Customer;
use Request;
use Auth;
use DB;
/**
* 
*/
class DashboardController extends Controller
{
	public function index()
	{
		if( Auth::user()->admin OR Auth::user()->Permissions()->first()->view_financial_dashboard )
		{
			$response = array();
			$members = Expert::with(['Appointments'])->where('company_id',Auth::user()->id)->where('active',1)->get();
			$team = array();
			foreach ($members as $member) {
				$data = $member->toArray();
				$data['sales'] = 0;
				foreach( $member->Appointments AS $a )
					$data['sales'] += $a->Transactions()->sum('amount');
				$data['appointments'] = $member->appointments->count();
				$team[] = $data;
			}
			$response['members'] = $team;
			unset($team);

			$ratings = Rating::where('created','<=',Request::input('end_date'))->where('created','>=',Request::input('start_date'))->with(['Customer','Expert'])->get()->toArray();
			$response['expert_ratings'] = $ratings;

			$experts = Expert::with(['Ratings'=>function($q){
				$q->with('Customer');
			},'Services'])->where('company_id',Auth::user()->company_id)->where('active',1);
			$response['experts'] = $experts->get();

			$response['new_users'] = Customer::where('created','<=',Request::input('end_date'))->where('created','>=',Request::input('start_date'))->where('active',1)
			->whereHas('Companies',function($q){
				$q->where('company_id',Auth::user()->company_id);
			})->count();

			$q = Appointment::active()->where('service_type',2);
			if( Request::has('start_date') )
				$q = $q->where('appointment_date','>=',Request::input('start_date'));
			if( Request::has('end_date') )
				$q = $q->where('appointment_date','<=',Request::input('end_date'));

			$response['new_appointment'] = $q->count();
			$response['sales'] = 0;
			foreach($q->get() AS $x )
				$response['sales'] += $x->Transactions()->sum('amount');

			$response['ratings'] = 0;
			$q = Rating::where('active',1);
			if( Request::has('start_date') )
				$q = $q->where('created','>=',Request::input('start_date'));
			if( Request::has('end_date') )
				$q = $q->where('created','<=',Request::input('end_date'));
			$response['ratings'] = $q->count();

			$response['graph'] = array(array(),array());
			$year = date('Y');
			for($i = 1;$i <=12;$i++)
			{
				$appointments = Appointment::active()->where('appointment_date','<',date('Y-m-01',strtotime($year."-".($i+1))))
									->where('appointment_date','>=',date('Y-m-01',strtotime($year."-".$i)))->with('Transactions')->get();

				$response['graph'][0][] = [$i,$appointments->count()];
				$total = 0;
				foreach( $appointments AS $appointment )
					$total += $appointment->Transactions->sum('amount');
				$response['graph'][1][] = [$i,$total];

			}
			
			return response()->json($response);
		}
		else
		{
			$response = array();
			$response['new_appointment'] = 0;
			$response['sales'] = 0;
			$response['ratings'] = 0;

			$q = Appointment::active()->where('expert_id',Auth::user()->id)->where('service_type',2);
			if( Request::has('start_date') )
				$q = $q->where('appointment_date','>=',Request::input('start_date'));
			if( Request::has('end_date') )
				$q = $q->where('appointment_date','<=',Request::input('end_date'));

			$response['new_appointment'] = $q->count();

			foreach($q->get() AS $x )
				$response['sales'] += $x->Transactions()->sum('amount');


			$q = Rating::where('expert_id',Auth::user()->id)->where('active',1);
			if( Request::has('start_date') )
				$q = $q->where('created','>=',Request::input('start_date'));
			if( Request::has('end_date') )
				$q = $q->where('created','<=',Request::input('end_date'));
			$response['ratings'] = $q->count();

			return response()->json($response);
		}
	}
}
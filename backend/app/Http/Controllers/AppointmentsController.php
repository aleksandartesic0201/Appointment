<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Company;
use App\Models\Appointment;
use App\Models\Expert;

use App\Models\State;
use App\Models\Country;

use App\Models\ExpertAppointment;
use App\Models\AppointmentTransaction;
use Stripe;
use Auth;
use Mail;
use DB;
use Config;
use \DateTime;
use \DateInterval;
use \DatePeriod;
class AppointmentsController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request) {
        $appointments = Appointment::with(['Expert', 'Customer'=>function($q){
                $q->with(['Country','State']);
            }, 'Transactions', 'Service'])->whereHas('Expert', function ($q) {
            $q->where('company_id', Auth::user()->company_id);
        });
        /*  2015/06/10 -ADDED BY LIU START - */
        $appointments = $appointments->whereHas('Customer',function($q){
            $q->where('active','<',"2");
        });
        /*  2015/06/10 -ADDED BY LIU END - */
        if ($request->has('cancelled')) {
            $appointments = $appointments->where('cancel_time', '!=', '0000-00-00 00:00:00');
        }
	    else
		    $appointments = $appointments->where('cancel_time','0000-00-00 00:00:00');
        $appointments = $appointments->active();
        
 /*       if ($request->has('expert_id')){ 
            $appointments = $appointments->byexpert($request->input('expert_id'));
        }
        */
        if ($request->has('type')) $appointments = $appointments->type($request->type);
        
        if ($request->has('start_date')) $appointments = $appointments->where('appointment_date', '>=', $request->input('start_date'));
        
        if ($request->has('end_date')) $appointments = $appointments->where('appointment_date', '<=', $request->input('end_date'));

        /*  2015/06/10 -ADDED BY LIU START - */
        $order = $request->input('order');
        if(!empty($order)){
            $order = reset($order);
            $columns = $request->input('columns');
            if($columns[$order['column']]['data'] != 'customer_name')
                $appointments = $appointments->orderBy($columns[$order['column']]['data'],$order['dir']);
        }
        /*  2015/06/10 -ADDED BY LIU END - */
        if( $request->input('expert_id') )
            $appointments = $appointments->where('expert_id',$request->input('expert_id'));

        $appointments = $appointments->orderBy('appointment_date', 'desc')->orderBy('start_time', 'ASC');

        if ($request->has('pagination')) $appointments = $appointments->paginate($request->has('length') ? $request->input('length') : 20);
        else $appointments = $appointments->get();
        $appointments->each(function ($q) {
            $c = $q->Expert->Appointments()->with('Transactions')->get();
            $q->total_appointments = $c->count();
            $q->total_amount = $q->Transactions->sum('amount');
        });
        
        if (!$request->has('pagination')) $appointments = ['data' => $appointments->toArray() ];
        else $appointments = $appointments->toArray();
        
        return response()->json($appointments);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //        
    }

    public function getFilters(Request $request) {
        $appointments = Appointment::with(['Expert', 'Customer'=>function($q){
                $q->with(['Country','State']);
            }, 'Transactions', 'Service'])->whereHas('Expert', function ($q) {
            $q->where('company_id', Auth::user()->company_id);
        });
        /*  2015/06/10 -ADDED BY LIU START - */
        $appointments = $appointments->whereHas('Customer',function($q){
            $q->where('active','<',"2");
        });
        /*  2015/06/10 -ADDED BY LIU END - */
        if ($request->has('cancelled')) {
            $appointments = $appointments->where('cancel_time', '!=', '0000-00-00 00:00:00');
        }
        else
            $appointments = $appointments->where('cancel_time','0000-00-00 00:00:00');
        $appointments = $appointments->active();
        
 /*       if ($request->has('expert_id')){ 
            $appointments = $appointments->byexpert($request->input('expert_id'));
        }
        */
        if ($request->has('type')) $appointments = $appointments->type($request->type);
        
        if ($request->has('start_date')) $appointments = $appointments->where('appointment_date', '>=', $request->input('start_date'));
        
        if ($request->has('end_date')) $appointments = $appointments->where('appointment_date', '<=', $request->input('end_date'));

        /*  2015/06/10 -ADDED BY LIU START - */
        $order = $request->input('order');
        if(!empty($order)){
            $order = reset($order);
            $columns = $request->input('columns');
            if($columns[$order['column']]['data'] != 'customer_name')
                $appointments = $appointments->orderBy($columns[$order['column']]['data'],$order['dir']);
        }
        /*  2015/06/10 -ADDED BY LIU END - */
        if( $request->input('expert_id') )
            $appointments = $appointments->where('expert_id',$request->input('expert_id'));

        $appointments = $appointments->orderBy('appointment_date', 'desc')->orderBy('start_time', 'ASC');

        if ($request->has('pagination')) $appointments = $appointments->paginate($request->has('length') ? $request->input('length') : 20);
        else $appointments = $appointments->get();
        $appointments->each(function ($q) {
            $c = $q->Expert->Appointments()->with('Transactions')->get();
            $q->total_appointments = $c->count();
            $q->total_amount = $q->Transactions->sum('amount');
        });
        
        if (!$request->has('pagination')) $appointments = ['data' => $appointments->toArray() ];
        else $appointments = $appointments->toArray();
        
        return response()->json($appointments);
    }
    
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request) {
        if ($request->input('appointment_type') == 'basic') return $this->storeBasicAppointment($request);
        
        $customer = $request->input('customer');
	    $dbCustomer = Customer::where('email', $customer['email'])->whereHas('Companies', function ($q) {
            $q->where('company_id', Auth::user()->company_id);
        })->first();
        $appointment = $request->except(['customer', 'payment']);
        $appointment['active'] = 1;
        $appointment['date_booked'] = date('Y-m-d');
        $service = Service::find($appointment['service_id']);
        $expert = Expert::find($appointment['expert_id']);
        if (!$dbCustomer) { 
            $company = Company::find(Auth::user()->company_id);
            $customer = Customer::create($customer);
            $plainPassword = str_random(8);
            $customer->password = bcrypt($plainPassword);
            DB::table('company_customer_relation')->insert(array('customer_id' => $customer->id, 'company_id' => $company->id));
            $emailData = array('customer' => $customer, 'service' => $service, 'company' => $company, 'customerPassword' => $plainPassword, 'expert' => $expert, 'appointment' => $appointment);
            Mail::send('emails.add_customer', $emailData, function ($message) use ($service, $company, $appointment, $customer) {
                $message->from($company->email, $company->name)->subject('Appointment Confirmation:  ' . $service->name . '  at ' . $company->name . ' on ' . $appointment['appointment_date'])->to($customer->email, $customer->name . " " . $customer->last_name);
            });
        } 
        else {
            $customer = $dbCustomer;
            Customer::where('id', $dbCustomer->id)->update($request->input('customer'));
        }
        
        $appointment['customer_id'] = $customer->id;
        $appointment['service_type'] = $service->type;
        $appointment['gap_in_min'] = $expert->unavailable_after_appointment;
        $appointment['last_time'] = date('H:i:s', strtotime($appointment['end_time']) + $expert->unavailable_after_appointment * 60);
	    $appointment['start_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$appointment['appointment_date']." ".$appointment['start_time']);
	    $appointment['start_time_eastern']->timezone = 'America/New_York';
	    $appointment['start_time_eastern'] = $appointment['start_time_eastern']->toDateTimeString();
	    $appointment['end_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$appointment['appointment_date']." ".$appointment['end_time']);
	    $appointment['end_time_eastern']->timezone = 'America/New_York';
	    $appointment['end_time_eastern'] = $appointment['end_time_eastern']->toDateTimeString();
        if ($appointment['service_type'] == 2) {
            $payment = $request->input('payment');
            try {
                $company = Company::find(Auth::user()->company_id);
                Config::set('services.stripe.secret', $company->stripe_api);
	            $existingAppointment = Appointment::where('appointment_date',$appointment['appointment_date'])->where('expert_id',$appointment['expert_id'])
		            ->where(function($q) use($appointment){
			            $q->whereBetween('start_time',[$appointment['start_time'],$appointment['end_time']])->orWhereBetween('end_time',[$appointment['start_time'],$appointment['end_time']]);
		            })
		            ->where('active',1)->where('cancel_time','0000-00-00 00:00:00')->first();
	            if( $existingAppointment )
		            return $this->error('This appointment overlaps with one of your other appointments. Please check your calendar to make sure there are no conflicing appointments for the date/time you selected.',500);
	            $appointment['payment_status'] = 1;
	            $appointment['start_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$appointment['appointment_date']." ".$appointment['start_time']);
	            $appointment['start_time_eastern']->timezone = 'America/New_York';
	            $appointment['start_time_eastern'] = $appointment['start_time_eastern']->toDateTimeString();
	            $appointment['end_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$appointment['appointment_date']." ".$appointment['end_time']);
	            $appointment['end_time_eastern']->timezone = 'America/New_York';
	            $appointment['end_time_eastern'] = $appointment['end_time_eastern']->toDateTimeString();
	            $oAppointment = Appointment::create($appointment);
                $charge = Stripe::charges()->create(['amount' => ($payment['cost'] - ($payment['cost'] * $payment['discount'] / 100)), 'currency' => $company->Currency()->first()->stripe_code, 'source' => ['number' => $payment['credit_card_number'], 'exp_month' => date('m', strtotime($payment['expiration_date'])), 'exp_year' => date('Y', strtotime($payment['expiration_date'])), 'cvc' => $payment['cvc'], ]]);
                if ($charge['id']) {
                    AppointmentTransaction::create(['transaction_id' => $charge['id'], 'appointment_id' => $oAppointment->id, 'payment_type' => 'stripe', 'amount' => $payment['cost'] - ($payment['cost'] * $payment['discount'] / 100), 'currency_id' => 1, 'status' => 'completed']);
                }
	            else {
		            $oAppointment->delete();
		            return $this->error('Payment has failed! Please try again.',500);
	            }
            }
            catch(\Exception $e) {
	            $oAppointment->delete();
                return response()->json(['error' => ['message' => $e->getMessage() ]], 500);
            }
        } 
        else {
	        $existingAppointment = Appointment::where('appointment_date',$appointment['appointment_date'])->where('expert_id',$appointment['expert_id'])
		        ->where(function($q) use($appointment){
			      $q->whereBetween('start_time',[$appointment['start_time'],$appointment['end_time']])->orWhereBetween('end_time',[$appointment['start_time'],$appointment['end_time']]);
		        })
		        ->where('active',1)->where('cancel_time','0000-00-00 00:00:00')->first();
	        if( $existingAppointment )
		        return $this->error('This appointment overlaps with one of your other appointments. Please check your calendar to make sure there are no conflicing appointments for the date/time you selected.',500);
            $appointment['payment_status'] = 2;
            $oAppointment = Appointment::create($appointment);
        }
        return response()->json($oAppointment->toArray());
    }
    
    private function storeBasicAppointment(Request $request) {
        $expertAppointmentdata = $request->all();
        $data['active'] = 1;
        if (isset($expertAppointmentdata['day']) && is_array($expertAppointmentdata['day'])) $expertAppointmentdata['day'] = implode(',', $expertAppointmentdata['day']);
        unset($expertAppointmentdata['appointment_type']);
        if ($expertAppointmentdata['is_repeated'] != '0') {
	        $expertAppointmentdata['repeat_on'] = $expertAppointmentdata['is_repeated'];
	        $expertAppointmentdata['is_repeated'] = 1;
        } 
        else $expertAppointmentdata['is_repeated'] = 0;
        
        $scheduledDates = [$expertAppointmentdata['appointment_date']];
	    if ($expertAppointmentdata['is_repeated'] == 1) {
            $scheduledDates = $this->get_dates($expertAppointmentdata['appointment_date'], $expertAppointmentdata['repeat_on'], $expertAppointmentdata['appointment_date'], $expertAppointmentdata['repeat_end'], $expertAppointmentdata['day']);
        }
        $data = $request->only(['start_time', 'end_time', 'name', 'description', 'expert_id']);
        $data['active'] = 1;
        $data['date_booked'] = date('Y-m-d');
        $data['service_type'] = 1;
        $data['payment_status'] = 2;
        $data['customer_id'] = 0;
	    $expert = Expert::find($data['expert_id']);
	    $data['gap_in_min'] = $expert->unavailable_after_appointment;
	    $data['last_time'] = date('H:i:s', strtotime($data['end_time']) + $expert->unavailable_after_appointment * 60);
        $data['appointment_type'] = $request->input('appointment_type');
	    $overlapping = false;
	    $appointments = Appointment::whereIn('appointment_date',$scheduledDates)->where('active',1)->where('expert_id',$expert->id)->get();
	    foreach( $scheduledDates AS $date ) {
		    $filteredAppointment = $appointments->filter(function($q) use($date){
			    return $q->appointment_date == $date;
		    });
		    $newAppointmentStartCarbon = Carbon::createFromFormat('Y-m-d H:i:s',$date." ".$data['start_time']);
		    $newAppointmentEndCarbon = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($date." ".$data['end_time']) + $expert->unavailable_after_appointment * 60 ) );
		    foreach ( $filteredAppointment AS $appointment ) {
			    $appointmentStartCarbon = Carbon::createFromFormat( 'Y-m-d H:i:s' , $appointment->appointment_date . " " . $appointment->start_time );
			    $appointmentEndCarbon = Carbon::createFromFormat( 'Y-m-d H:i:s' , date('Y-m-d H:i:s',strtotime($appointment->appointment_date . " " . $appointment->end_time) + $expert->unavailable_after_appointment * 60 ) );
			    if ( $newAppointmentStartCarbon->between( $appointmentStartCarbon , $appointmentEndCarbon , TRUE ) OR $newAppointmentEndCarbon->between( $appointmentStartCarbon , $appointmentEndCarbon , TRUE ) ) {
				    $overlapping = TRUE;
				    return $this->error('This appointment overlaps with one of your other appointments. Please check your calendar to make sure there are no conflicing appointments for the date/time you selected.',500);
					//return $this->error( 'Your appointment is overlapping with another appointment '.$appointment->name.' at ' . $appointmentStartCarbon->toDateString() , 500 );
			    }

		    }
	    }
        if( !$expertAppointmentdata['is_repeated'] )
        {
            $date = $expertAppointmentdata['appointment_date'];
            $appointments = Appointment::where('appointment_date',$expertAppointmentdata['appointment_date'])->where('active')->where('expert_id',$expert->id)->get();
            $newAppointmentStartCarbon = Carbon::createFromFormat('Y-m-d H:i:s',$date." ".$data['start_time']);
            $newAppointmentEndCarbon = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($date." ".$data['end_time']) + $expert->unavailable_after_appointment * 60 ) );
            foreach ( $appointments AS $appointment ) {
                $appointmentStartCarbon = Carbon::createFromFormat( 'Y-m-d H:i:s' , $appointment->appointment_date . " " . $appointment->start_time );
                $appointmentEndCarbon = Carbon::createFromFormat( 'Y-m-d H:i:s' , date('Y-m-d H:i:s',strtotime($appointment->appointment_date . " " . $appointment->end_time) + $expert->unavailable_after_appointment * 60 ) );
                if ( $newAppointmentStartCarbon->between( $appointmentStartCarbon , $appointmentEndCarbon , TRUE ) OR $newAppointmentEndCarbon->between( $appointmentStartCarbon , $appointmentEndCarbon , TRUE ) ) {
                    $overlapping = TRUE;
                    return $this->error('This appointment overlaps with one of your other appointments. Please check your calendar to make sure there are no conflicing appointments for the date/time you selected.',500);
                    //return $this->error( 'Your appointment is overlapping with another appointment '.$appointment->name.' at ' . $appointmentStartCarbon->toDateString() , 500 );
                }

            }
        }
	    if(! $overlapping ) {
		    $expert_appointment = ExpertAppointment::create($expertAppointmentdata);
		    $data['expert_appointment_id'] = $expert_appointment->id;
		    foreach ( $scheduledDates as $date ) {
			    $data[ 'appointment_date' ] = $date;
			    $data['start_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$data['appointment_date']." ".$data['start_time']);
			    $data['start_time_eastern']->timezone = 'America/New_York';
			    $data['start_time_eastern'] = $data['start_time_eastern']->toDateTimeString();
			    $data['end_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$data['appointment_date']." ".$data['end_time']);
			    $data['end_time_eastern']->timezone = 'America/New_York';
			    $data['end_time_eastern'] = $data['end_time_eastern']->toDateTimeString();
			    $company_appointment = Appointment::create( $data );
		    }
	    }
        
        return response()->json($company_appointment->toArray());
    }
    
    private function get_dates($appointment_date, $repeats_on, $repeat_start, $repeat_end, $days) {
        
        if ($repeats_on == 'Weekly' || $repeats_on == 'Bi-Weekly') {
            $daysArr = explode(',', $days);
            
            $start = new DateTime($repeat_start);
            $end = new DateTime($repeat_end);
            $end->modify('+1 day');
            
            $interval = new DateInterval('P1D');
            
            $period = new DatePeriod($start, $interval, $end);
            
            // only trigger every three weeks...
            $weekInterval = ($repeats_on == 'Weekly') ? 1 : 2;
            
            // initialize fake week
            $fakeWeek = 0;
            $currentWeek = $start->format('W');
            
            foreach ($period as $date) {
                
                if ($date->format('W') !== $currentWeek) {
                    $currentWeek = $date->format('W');
                    $fakeWeek++;
                }
                
                if ($fakeWeek % $weekInterval !== 0) {
                    continue;
                }
                
                $dayOfWeek = $date->format('l');
                if (in_array($dayOfWeek, $daysArr)) {
                    $shedule_dates[] = $date->format('Y-m-d');
                }
            }
        } 
        else if ($repeats_on == 'Monthly') {
            $fix_date = date('d', strtotime($repeat_start));
            $start = new DateTime(date('Y-m-', strtotime($repeat_start)) . '01');
            $end = new DateTime($repeat_end);
            $end->modify('+1 day');
            
            $interval = new DateInterval('P1M');
            $daterange = new DatePeriod($start, $interval, $end);
            
            foreach ($daterange as $date) {
                if (checkdate($date->format('m'), $fix_date, $date->format('Y'))) {
                    $shedule_dates[] = $date->format('Y-m-' . $fix_date);
                }
            }
        } 
        else if ($repeats_on == 'Yearly') {
            $fix_date = date('m-d', strtotime($repeat_start));
            $fix_day = date('d', strtotime($repeat_start));
            $fix_month = date('m', strtotime($repeat_start));
            
            $start = new DateTime(date('Y-', strtotime($repeat_start)) . '01-01');
            $end = new DateTime($repeat_end);
            $end->modify('+1 day');
            
            $interval = new DateInterval('P1Y');
            $daterange = new DatePeriod($start, $interval, $end);
            
            foreach ($daterange as $date) {
                if (checkdate($fix_month, $fix_day, $date->format('Y'))) {
                    $shedule_dates[] = $date->format('Y-' . $fix_date);
                }
            }
        }
        return $shedule_dates;
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $appointment = Appointment::with(['Service', 'Customer' => function ($q) {
            $q->with(['State', 'Country']);
        }, 'Expert'])->find($id);
        if (!$appointment) return $this->error('Appointment not found!');
        if( !empty( $appointment->expert_appointment_id ) )
        {
	        $e = ExpertAppointment::find($appointment->expert_appointment_id);
            $appointment->repeat_end = $e->repeat_end;
	        $appointment->expert_appointment = $e->toArray();
        }
        return response()->json($appointment->toArray());
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        
        //
        
        
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
	    $expert = Expert::find($request->input('expert_id'));
        if ($request->input('appointment_type') == 'basic') {
            $expertAppointment = ExpertAppointment::find($request->input('expert_appointment_id') );
	        $scheduledDates = [$expertAppointment->appointment_date];
	        if ($expertAppointment->is_repeated == 1) {
		        $scheduledDates = $this->get_dates($expertAppointment->appointment_date, $expertAppointment->repeat_on, $expertAppointment->appointment_date, $request->input('repeat_end'), $expertAppointment->day);
	        }
	        $appointments = Appointment::whereIn('appointment_date',$scheduledDates)->where('active',1)->where('expert_id',$expert->id)->where('expert_appointment_id','!=',$expertAppointment->id)->get();
	        foreach( $scheduledDates AS $date ) {
		        $filteredAppointment = $appointments->filter(function($q) use($date){
			        return $q->appointment_date == $date;
		        });
		        $newAppointmentStartCarbon = Carbon::createFromFormat('Y-m-d H:i:s',$date." ".$request->input('start_time'));
		        $newAppointmentEndCarbon = Carbon::createFromFormat('Y-m-d H:i:s',date('Y-m-d H:i:s',strtotime($date." ".$request->input('end_time')) + $expert->unavailable_after_appointment * 60 ) );
		        foreach ( $filteredAppointment AS $appointment ) {
			        $appointmentStartCarbon = Carbon::createFromFormat( 'Y-m-d H:i:s' , $appointment->appointment_date . " " . $appointment->start_time);
			        $appointmentEndCarbon = Carbon::createFromFormat( 'Y-m-d H:i:s' , date('Y-m-d H:i:s',strtotime($appointment->appointment_date . " " . $appointment->end_time) + $expert->unavailable_after_appointment * 60 ) );
			        if ( $newAppointmentStartCarbon->between( $appointmentStartCarbon , $appointmentEndCarbon , TRUE ) OR $newAppointmentEndCarbon->between( $appointmentStartCarbon , $appointmentEndCarbon , TRUE ) ) {
				        $overlapping = TRUE;
				        return $this->error('This appointment overlaps with one of your other appointments. Please check your calendar to make sure there are no conflicing appointments for the date/time you selected.',500);
				        //return $this->error( 'Your appointment is overlapping with another appointment '.$appointment->name.' at ' . $appointmentStartCarbon->toDateString() , 500 );
			        }

		        }
	        }
            if( $expertAppointment->repeat_end != $request->input('repeat_end') )
            {
                Appointment::where('expert_appointment_id',$expertAppointment->id)->delete();//where('appointment_date','>',date('Y-m-d'))->delete();

                $data = $request->only(['start_time', 'end_time', 'name', 'description']);
                $data['expert_id'] = $expertAppointment->expert_id;
                $data['active'] = 1;
                $data['date_booked'] = date('Y-m-d');
                $data['expert_appointment_id'] = $expertAppointment->id;
                $data['service_type'] = 1;
                $data['payment_status'] = 2;
                $data['customer_id'] = 0;
                $data['appointment_type'] = 'basic';
	            $data['gap_in_min'] = $expert->unavailable_after_appointment;
	            $data['last_time'] = date('H:i:s', strtotime($data['end_time']) + $expert->unavailable_after_appointment * 60);
	            $overlapping = false;

	            if(! $overlapping ) {
		            foreach ( $scheduledDates as $date ) {
			            $data[ 'appointment_date' ] = $date;
			            $data['start_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$data['appointment_date']." ".$data['start_time']);
			            $data['start_time_eastern']->timezone = 'America/New_York';
			            $data['start_time_eastern'] = $data['start_time_eastern']->toDateTimeString();
			            $data['end_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$data['appointment_date']." ".$data['end_time']);
			            $data['end_time_eastern']->timezone = 'America/New_York';
			            $data['end_time_eastern'] = $data['end_time_eastern']->toDateTimeString();
			            $company_appointment = Appointment::create( $data );
		            }
	            }
                $expertAppointment->repeat_end = $request->input('repeat_end');
                $expertAppointment->save();
                return response()->json($company_appointment->toArray());
            }
            else
            {
                $data = $request->only(['name', 'description', 'start_time', 'end_time']);
	            $appointments = Appointment::where('expert_appointment_id',$request->input('expert_appointment_id'))->get();
	            $appointments->each(function($q) use($data){
		           $q->start_time_eastern = Carbon::createFromFormat('Y-m-d H:i:s',$q->appointment_date." ".$data['start_time']);
		           $q->end_time_eastern = Carbon::createFromFormat('Y-m-d H:i:s',$q->appointment_date." ".$data['end_time']);
		            $q->start_time_eastern->timezone = 'America/New_York';
		            $q->start_time_eastern = $q->start_time_eastern->toDateTimeString();
		            $q->end_time_eastern->timezone = 'America/New_York';
		            $q->end_time_eastern = $q->end_time_eastern->toDateTimeString();
		            $q->start_time = $data['start_time'];
		            $q->end_time = $data['end_time'];
		            $q->description = $data['description'];
		            $q->name = $data['name'];
		            $q->save();
	            });
	            ExpertAppointment::where('id', $request->input('expert_appointment_id'))->update($data);
            }
        } 
        else {
            $data = $request->except(['customer', 'refund', 'service', 'expert', 'cancel']);
            $expert = Expert::find($data['expert_id']);
            $data['last_time'] = date('H:i:s',strtotime($data['end_time']) + $expert->unavailable_after_appointment * 60 );
	        if(! $request->has('cancel') ) {
		        $existingAppointment = Appointment::where( 'appointment_date' , $data[ 'appointment_date' ] )->where( 'expert_id' , $data[ 'expert_id' ] )->where( 'id' , '!=' , $data[ 'id' ] )
			        ->where( function ( $q ) use ( $data ) {
				        $q->whereBetween( 'start_time' , [ $data[ 'start_time' ] , $data[ 'end_time' ] ] )->orWhereBetween( 'end_time' , [ $data[ 'start_time' ] , $data[ 'end_time' ] ] );
			        } )
			        ->where( 'active' , 1 )->where( 'cancel_time' , '0000-00-00 00:00:00' )->first();
		        if ( $existingAppointment )
			        return $this->error( 'This appointment overlaps with one of your other appointments. Please check your calendar to make sure there are no conflicing appointments for the date/time you selected.' , 500 );
	        }
	        $data['start_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$data['appointment_date']." ".$data['start_time']);
	        $data['start_time_eastern']->timezone = 'America/New_York';
	        $data['start_time_eastern'] = $data['start_time_eastern']->toDateTimeString();
	        $data['end_time_eastern'] = Carbon::createFromFormat('Y-m-d H:i:s',$data['appointment_date']." ".$data['end_time']);
	        $data['end_time_eastern']->timezone = 'America/New_York';
	        $data['end_time_eastern'] = $data['end_time_eastern']->toDateTimeString();
            Appointment::where('id', $id)->update($data);
            $appointment = Appointment::with(['Service', 'Customer', 'Expert'])->find($id);
            $company = Company::find(Auth::user()->company_id);
            $service = $appointment->Service;
            $customer = $appointment->Customer;
            $expert = $appointment->Expert;
            $emailData = array('customer' => $customer, 'service' => $service, 'company' => $company, 'expert' => $expert, 'appointment' => $appointment);

	        if ($appointment->service_type == 2 && $request->has('cancel')) {
                try {
                    Config::set('services.stripe.secret', $company->stripe_api);
                    $transaction = $appointment->Transactions()->first();
                    if ($transaction) {
                        $refund = $request->input('refund');
                        $emailData['refund'] = $refund['percentage'];
                        $amount = $transaction->amount;
                        if( isset( $refund['percentage']))
                            $amount = ($transaction->amount - ($transaction->amount * $refund['percentage'] / 100));
                        $refund = Stripe::refunds()->create($transaction->transaction_id, $amount);
	                    if ($refund['id']) {
                            $appointment->refund_id = $refund['id'];
                        }
                    }
                }
                catch(\Exception $e) {
                    return response()->json(['error' => ['message' => $e->getMessage() ]], 500);
                }
            }
	        if ($request->has('cancel')) {
		        $appointment->cancel_time = date('Y-m-d H:i:s');
                /* 2015/06/09 DELETED BY LIU START*/
		        //$appointment->cancelled_by = Auth::user()->admin ? 'Admin' : Auth::user()->firstname." ".Auth::user()->lastname;
                /* 2015/06/09 DELETED BY LIU END*/
                /* 2015/06/09 UPDATED BY LIU START*/
                $appointment->cancelled_by = Auth::user()->firstname." ".Auth::user()->lastname;
                /* 2015/06/09 UPDATED BY LIU START*/
		        //$data['active'] = 0;
		        $appointment->save();
		        Mail::send('emails.cancel_appointment', $emailData, function ($message) use ($service, $company, $appointment, $customer) {
			        $message->from($company->email, $company->name)->subject('Appointment Cancellation:  ' . $service->name . '  at ' . $company->name . ' on ' . $appointment->appointment_date)->to($customer->email, $customer->name . " " . $customer->last_name);
		        });
	        } else
	        {
		        Mail::send('emails.modify_appointment', $emailData, function ($message) use ($service, $company, $appointment, $customer) {
			        $message->from($company->email, $company->name)->subject('Appointment Confirmation:  ' . $service->name . '  at ' . $company->name . ' on ' . $appointment->appointment_date)->to($customer->email, $customer->name . " " . $customer->last_name);
		        });
	        }
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id, Request $request) {
        $appointment = Appointment::find($id);
        if ($appointment->appointment_type == 'basic') {
            if ($request->has('delete_repeating')) {
                Appointment::where('expert_appointment_id', $appointment->expert_appointment_id)->delete();//where('appointment_date', '>', date('Y-m-d'))->delete();
                ExpertAppointment::where('id', $appointment->expert_appointment_id)->delete();
            } 
            else Appointment::where('id', $id)->delete();
        }
        return response()->json(['success' => 'Appointment deleted']);
    }
}

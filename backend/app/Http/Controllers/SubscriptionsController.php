<?php namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Subscription;
use Auth;
use Config;
use Illuminate\Support\Facades\Log;
use Mail;
use PhpSpec\Exception\Exception;
use Stripe;
use App\Models\Package;
use Request;

class SubscriptionsController extends Controller
{
	public function index()
	{
		$company = Company::find(Auth::user()->company_id);
		//Config::set('services.stripe.secret', $company->stripe_api);
		$invoices = Stripe::invoices()->all(['customer'=>$company->stripe_account]);
		$response = array('history'=>[]);
		foreach($invoices['data'] as $invoice )
		{
			if( $invoice['subscription'] )
			{
				$invoiceData = array();
				$items = Stripe::invoices()->lineItems($invoice['id'],['limit'=>100]);
				foreach( $items as $item )
				{
					$invoiceData['items'] = $item;
				}
				$response['history'][] = $invoiceData;
			}
		}
		$subscription = Subscription::where('company_id',Auth::user()->company_id)->first();
		if( $subscription )
		{
			$package = Package::find($subscription->package_id);
			$subscription->package = $package;
		}
		$response['current'] = $subscription;
		return response()->json($response);
	}

	public function update($id)
	{
		$plan = Package::find(Request::input('plan'));
		$subscription = Subscription::find(Request::input('id'));
		/* 2015/06/14 ADDED BY LIU START */
		$start_date = date("Y-m-d");
		$end_date = date("Y-m-d", strtotime('next month'));
		$payment_attempted_at = date("Y-m-d");
		/* 2015/06/14 ADDED BY LIU END */
		$stripe = Stripe::subscriptions()->update($subscription->customer_id,$subscription->subscription_id,['plan'=>$plan->stripe_id]);
		$subscription->package_id = $plan->id;
		/* 2015/06/14 ADDED BY LIU START */
		$subscription->start = $start_date;
		$subscription->end = $end_date;
		$subscription->payment_attempted_at = $payment_attempted_at;
		/* 2015/06/14 ADDED BY LIU END */
		$subscription->save();
		$invoice = Stripe::invoices()->upcomingInvoice($subscription->customer_id,$subscription->subscription_id);
		$company = Company::find($subscription->company_id);
		Mail::send('emails.plan_change', ['company'=>$company,'sub'=>$invoice,'plan'=>$plan], function ($message) use ($company) {
			$message->to($company->email, $company->name)->subject('Subscription Updated');
		});
		return $this->index();
	}

	public function updateCard()
	{
		$card = Request::all();
		$card['object'] = 'card';
		$company = Company::find(Auth::user()->company_id);
		$cards = Stripe::cards()->all($company->stripe_account);
		$create = true;
		foreach($cards['data'] AS $c )
		{
			if( $c['exp_month'] == $card['exp_month'] && $c['exp_year'] == $card['exp_year'] && $c['last4'] == substr($card['number'],-4))
			{
				$create = false;
				$card['id'] = $c['id'];
				break;
			}
		}
		try {
			if( $create )
				$card = Stripe::cards()->create($company->stripe_account,$card);
			$customer = Stripe::customers()->update($company->stripe_account,['default_source'=>$card['id']]);
		} catch(\Exception $e) {
			return $this->error($e->getMessage(),500);
		}
	}

	public function paymentDone()
	{
		$data = @file_get_contents('php://input');
		$event = json_decode($data);
		if( $event->type == 'invoice.payment_succeeded' )
		{
			$subscriptionData = array();
			$subscriptionData['customer_id'] = $event->data->object->customer;
			$subscriptionData['subscription_id'] = $event->data->object->subscription;
			$subscriptionData['amount'] = $event->data->object->total / 100;
			$subscriptionData['start'] = date('Y-m-d',$event->data->object->period_start);
			$subscriptionData['end'] = date('Y-m-d',$event->data->object->period_end);
			$subscriptionData['invoice_id'] = $event->data->object->id;

			$package = Package::where('stripe_id',$event->data->object->lines->data[0]->plan->id)->first();
			$subscriptionData['package_id'] = $package->id;
			$company = Company::where('stripe_account',$subscriptionData['customer_id'])->first();
			$subscriptionData['company_id'] = $company ? $company->id : 0;
			$subscriptionData['created'] = $subscriptionData['payment_attempted_at'] = date('Y-m-d');

			$subscription = Subscription::where('customer_id',$subscriptionData['customer_id'])->first();
			if( $subscription )
				Subscription::where('id',$subscription->id)->update($subscriptionData);
			else
				Subscription::create($subscriptionData);
		}
		if( $event->type == 'invoice.payment_failed' )
		{
			$s = Subscription::where('customer_id',$event->data->object->customer)->first();
			$s->active = 0;
			$s->payment_attempted_at = date('Y-m-d');
			$s->save();
		}

		return "";
	}
}
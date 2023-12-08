<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {

	protected $table = 'company_customers';
	protected $guarded = array('id');
	public $timestamps = false;

	public function Appointments()
	{
		return $this->hasMany('App\Models\Appointment','customer_id');
	}

	public function Country()
	{
		return $this->belongsTo('App\Models\Country','country_id');
	}

	public function Companies()
	{
		return $this->belongsToMany('App\Models\Company','company_customer_relation','customer_id');
	}

	public function State()
	{
		return $this->belongsTo('App\Models\State','state_id');
	}

	/* 2015/06/10 - ADDED BY LIU START- */
	public function Rating()
	{
		return $this->belongsTo('App\Models\Rating','rated_by');
	}
	/* 2015/06/10 - ADDED BY LIU END- */

}

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model {

	protected $table = 'company_expert_ratings';

	protected $guarded = array('id');

	public $timestamps = false;

	public function Expert()
	{
		return $this->belongsTo('App\Models\Expert','expert_id');
	}


	public function Customer()
	{
		return $this->belongsTo('App\Models\Customer','rated_by');
	}


	public function Appointment()
	{
		return $this->belongsTo('App\Models\Appointment','appointment_id');
	}

}

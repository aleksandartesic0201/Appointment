<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {

	protected $table = 'company_services';

	public $timestamps = false;

	protected $guarded = array('id');

	public function Experts()
	{
		return $this->belongsToMany('App\Models\Expert','expert_services','service_id');
	}

	/* 2015/06/11 -ADDED BY LIU START- */
	public function Appointments()
	{
		return $this->hasMany('App\Models\Appointment','service_id');
	}
	/* 2015/06/11 -ADDED BY LIU END- */

}

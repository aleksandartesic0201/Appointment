<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model {

	protected $table = 'company_appointments';
	protected $guarded = array('id');
	public $timestamps = false;

	public function Expert()
	{
		return $this->belongsTo('App\Models\Expert','expert_id');
	}

	public function Customer()
	{
		return $this->belongsTo('App\Models\Customer','customer_id');
	}

	public function Transactions()
	{
		return $this->hasMany('App\Models\AppointmentTransaction','appointment_id');
	}

	public function Service()
	{
		return $this->belongsTo('App\Models\Service','service_id');
	}

	public function ExpertAppointment()
	{
		return $this->belongsTo('App\Models\ExpertAppointment','expert_appointment_id');
	}

	public function scopeActive($query)
	{
		return $query->where('active',1);
	}

	public function scopeType($query,$type = 'free')
	{
		if( $type == 'free')
			$id = 1;
		else if( $type == 'prepaid')
			$id = 2;
		else if( $type == 'postpaid')
			$id = 3;
		return $query->where('service_type',$id);
	}

	public function scopeByExpert($query,$id)
	{
		return $query->where('expert_id',$id);
	}

}

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertAppointment extends Model {

	protected $table = 'expert_appointments';

	public $timestamps = false;

	protected $guarded = ['id'];

	public function Expert()
	{
		return $this->belongsTo('App\Models\Expert','expert_id');
	}

}

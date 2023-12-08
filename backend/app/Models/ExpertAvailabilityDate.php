<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertAvailabilityDate extends Model {

	protected $table = 'expert_availability_date';

	protected $guarded = array('id');

	public $timestamps = false;

	public function Expert()
	{
		return $this->belongsTo('App\Models\Expert','expert_id');
	}

	public function scopeDate($query,$date)
	{
		return $query->where('ondate',$date);
	}

	public function scopeDay($query,$day)
	{
		return $query->where('day',$day);
	}

}

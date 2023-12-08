<?php namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ExpertDefaultAvailability extends Model
{
	protected $table = 'expert_availability_default';

	protected $guarded = array('id');

	public $timestamps = false;

	public function Expert()
	{
		return $this->belongsTo('App\Models\Expert','expert_id');
	}

	public function scopeDay($query,$day)
	{
		return $query->where('day',$day);
	}
}
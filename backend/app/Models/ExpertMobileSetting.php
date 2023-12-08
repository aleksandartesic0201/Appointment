<?php namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ExpertMobileSetting extends Model
{
	protected $table = 'expert_mobile_settings';

	protected $guarded = array('id');

	public $timestamps = false;

	public function Expert()
	{
		return $this->belongsTo('App\Models\Expert','expert_id');
	}
}
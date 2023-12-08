<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertPermission extends Model {

	protected $table = "expert_permissions";

	public $timestamps = false;

	protected $guarded = array('id');

	public function Expert()
	{
		return $this->belongsTo('Aoo\Models\Expert','expert_id');
	}

}

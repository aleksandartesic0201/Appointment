<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model {
/*  2015/06/10 -ADDED BY LIU START - */
	protected $table = 'states';

	public $timestamps = false;

	protected $guarded = array('id');
 
	public function Expert()
	{
		return $this->belongsTo('App\Models\Expert','state_id');
	}
/*  2015/06/10 -ADDED BY LIU END - */
}

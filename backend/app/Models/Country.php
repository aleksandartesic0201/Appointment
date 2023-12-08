<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {

	//
	//
	public function States()
	{
		return $this->hasMany('App\Models\State','country_id');
	}
	/* 2014/06/13 ADDED BY LIU START */
	public function Customers()
	{
		return $this->belongsTo('App\Models\Customer','country_id');
	}
	/* 2014/06/13 ADDED BY LIU END */

}

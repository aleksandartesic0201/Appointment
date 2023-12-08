<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Company extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	public $timestamps = false;
	protected $hidden = [ "password" ];
	//
	/* 2014/06/13 UPDATED BY LIU START */
	public function Customers()
	{
		return $this->belongsToMany('App\Models\Customer','company_customer_relation','company_id');
	}
	/* 2014/06/13 UPDATED BY LIU END */

	public function Experts()
	{
		return $this->hasMany('App\Models\Expert','company_id');
	}

	public function Services()
	{
		return $this->hasMany('App\Models\Service','company_id');
	}

	public function HeaderLinks()
	{
		return $this->hasMany('App\Models\HeaderLink','company_id');
	}

	public function FooterLinks()
	{
		return $this->hasMany('App\Models\FooterLink','company_id');
	}

	public function News()
	{
		return $this->hasMany('App\Models\News','company_id');
	}
	public function State()
	{
		return $this->belongsTo('App\Models\State','state_id');
	}
	public function Timezone()
	{
		return $this->belongsTo('App\Models\Timezone','time_zone_setting');
	}

	public function Currency()
	{
		return $this->belongsTo('App\Models\Currency','currency_id');
	}
}

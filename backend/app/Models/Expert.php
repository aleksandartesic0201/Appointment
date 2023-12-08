<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Expert extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'experts';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password'];

	public $timestamps = false;

	public function Appointments()
	{
		return $this->hasMany('App\Models\Appointment','expert_id');
	}

	public function Company() {
		return $this->belongsTo('App\Models\Company','company_id');
	}

	/*  2015/06/10 -ADDED BY LIU START - */
	public function State()
	{
		return $this->belongsTo('App\Models\State','state_id');
	}
	/*  2015/06/10 -ADDED BY LIU END - */

	public function Ratings()
	{
		return $this->hasMany('App\Models\Rating','expert_id');
	}

	public function Services()
	{
		return $this->belongsToMany('App\Models\Service','expert_services','expert_id');
	}

	public function Permissions()
	{
		return $this->hasOne('App\Models\ExpertPermission','expert_id');
	}

	public function DateAvailability()
	{
		return $this->hasMany('App\Models\ExpertAvailabilityDate','expert_id');
	}

	public function DefaultAvailability()
	{
		return $this->hasMany('App\Models\ExpertDefaultAvailability','expert_id');
	}

	public function scopeActive($query)
	{
		return $query->where('active',1);
	}
}

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentTransaction extends Model {

	protected $table = "company_appointment_transaction";
	protected $guarded = array('id');
	public $timestamps = false;

}

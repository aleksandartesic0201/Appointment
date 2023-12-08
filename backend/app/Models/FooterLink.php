<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model {

	protected $table = "company_footer_links";

	protected $guarded = [ 'id' ];

	public $timestamps = false;

	public function Company()
	{
		return $this->belongsTo('App\Models\Company','company_id');
	}
}

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderLink extends Model {

	protected $table = "company_header_links";

	protected $guarded = [ 'id' ];

	public $timestamps = false;

	public function Company()
	{
		return $this->belongsTo('App\Models\Company','company_id');
	}
}

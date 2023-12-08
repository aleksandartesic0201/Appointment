<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model {

	protected $table = 'company_news';

	public $timestamps = false;

	protected $guarded = ['id'];

	public function Company() {
		return $this->belongsTo('App\Models\Company','company_id');
	}
}

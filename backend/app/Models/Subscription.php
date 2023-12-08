<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {

	protected $table = 'company_subscriptions';

	public $timestamps = false;

	protected $guarded = ['id'];
}

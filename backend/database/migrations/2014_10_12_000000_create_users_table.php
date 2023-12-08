<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('firstname')->nullable()->default(null);
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->tinyInteger('is_administrator')->nullable()->default(0);
			$table->string('lastname')->nullable()->default(null);
			$table->string('username')->nullable()->default(null);
			$table->integer('country_id')->nullable()->default(null);
			$table->integer('state_id')->nullable()->default(null);
			$table->string('city')->nullable()->default(null);
			$table->string('address')->nullable()->default(null);
			$table->string('suite')->nullable()->default(null);
			$table->string('zip')->nullable()->default(null);
			$table->tinyInteger('role')->nullable()->default(null);
			$table->smallInteger('status')->nullable()->default(null);
			$table->string('service_email')->nullable()->default(null);
			$table->string('service_phone')->nullable()->default(null);
			$table->string('stripe')->nullable()->default(null);
			$table->string('token')->nullable()->default(null);
			$table->softDeletes();
			$table->rememberToken();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}

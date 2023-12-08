<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->nullable()->default(null);
			$table->integer('country_id')->nullable()->default(null);
			$table->integer('state_id')->nullable()->default(null);
			//$table->string('stripe')->nullable()->default(null);
			$table->string('firstname')->nullable()->default(null);
			$table->string('lastname')->nullable()->default(null);
			$table->string('email')->nullable()->default(null);
			//$table->string('password')->nullable()->default(null);
			$table->string('mobile')->nullable()->default(null);
			$table->string('address')->nullable()->default(null);
			$table->string('city')->nullable()->default(null);
			$table->string('zip')->nullable()->default(null);
			$table->softDeletes();
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
		Schema::drop('customers');
	}

}

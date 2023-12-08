<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_services', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->nullable()->default(null);
			$table->integer('type')->nullable()->default(null);
			$table->string('name')->nullable()->default(null);
			$table->string('description')->nullable()->default(null);
			$table->integer('minutes')->nullable()->default(null);
			$table->float('price')->nullable()->default(null);
			$table->string('icon')->nullable()->default(null);
			$table->string('images',500)->nullable()->default(null);
			$table->string('appointment_notification_text')->nullable()->default(null);
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
		Schema::drop('company_services');
	}

}

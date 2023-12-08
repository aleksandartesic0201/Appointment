<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('appointments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->nullable()->default(null);
			$table->integer('expert_id')->nullable()->default(null);
			$table->enum('type',['basic','advance'])->nullable()->default(null);
			$table->integer('customer_id')->nullable()->default(null);
			$table->integer('service_id')->nullable()->default(null);
			$table->integer('expert_appointment_id')->nullable()->default(null);
			$table->string('name')->nullable()->default(null);
			$table->string('description')->nullable()->default(null);
			$table->string('signature_file')->nullable()->default(null);
			$table->date('booked_on')->nullable()->default(null);
			$table->date('appointment_on')->nullable()->default(null);
			$table->time('starts_at')->nullable()->default(null);
			$table->time('ends_at')->nullable()->default(null);
			$table->integer('gap_in_minutes')->nullable()->default(null);
			$table->time('last_time')->nullable()->default(null);
			$table->tinyInteger('service_type')->nullable()->default(null);
			$table->tinyInteger('payment_status')->nullable()->default(null);
			$table->dateTime('cancel_at')->nullable()->default(null);
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
		Schema::drop('appointments');
	}

}

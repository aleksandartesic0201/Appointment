<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_appointment_transaction', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('appointment_id')->nullable()->default(null);
			$table->string('payment_type')->nullable()->default(null);
			$table->string('txn_id')->nullable()->default(null);
			$table->float('amount')->nullable()->default(null);
			$table->integer('currency_id')->nullable()->default(null);
			$table->string('status')->nullable()->default(null);
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
		Schema::drop('company_appointment_transaction');
	}

}

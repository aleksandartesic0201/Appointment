<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_payment_history', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->nullable()->default(null);
			$table->integer('package_id')->nullable()->default(null);
			$table->string('stripe_customer_id')->nullable()->default(null);
			$table->string('subscription_id')->nullable()->default(null);
			$table->dateTime('starts_at')->nullable()->default(null);
			$table->dateTime('ends_at')->nullable()->default(null);
			$table->float('amount')->nullable()->default(null);
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
		Schema::drop('company_payment_history');
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_subscriptions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->nullable()->default(null);
			$table->integer('package_id')->nullable()->default(null);
			$table->string('subscription_id')->nullable()->default(null);
			$table->integer('stripe_plan_id')->nullable()->default(null);
			$table->string('stripe_customer_id')->nullable()->default(null);
			$table->float('monthly_cost')->nullable()->default(null);
			$table->float('setup_cost')->nullable()->default(null);
			$table->date('start_date')->nullable()->default(null);
			$table->float('firstmon_amount')->nullable()->default(null);
			$table->float('month_amount')->nullable()->default(null);
			$table->string('name_on_card')->nullable()->default(null);
			$table->dateTime('starts_at')->nullable()->default(null);
			$table->dateTime('ends_at')->nullable()->default(null);
			$table->tinyInteger('type')->nullable()->default(null);
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
		Schema::drop('company_subscriptions');
	}

}

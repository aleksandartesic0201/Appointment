<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreateCompaniesTablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('companies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->nullable()->default(null);
			$table->string('contact_name')->nullable()->default(null);
			//$table->string('contact_name_title')->nullable()->default(null);
			$table->string('address')->nullable()->default(null);
			$table->integer('country_id')->nullable()->default(null);
			$table->integer('state_id')->nullable()->default(null);
			$table->string('city')->nullable()->default(null);
			$table->string('suit')->nullable()->default(null);
			$table->string('zip')->nullable()->default(null);
			$table->string('website')->nullable()->default(null);
			$table->string('email')->nullable()->default(null);
			$table->string('phone_number')->nullable()->default(null);
			$table->string('company_phone')->nullable()->default(null);
			$table->string('company_email')->nullable()->default(null);
			$table->integer('currency_id')->nullable()->default(null);
			$table->text('notes')->nullable()->default(null);
			$table->string('background',50)->nullable()->default(null);
			$table->string('foreground',50)->nullable()->default(null);
			$table->string('font_type',50)->nullable()->default(null);
			$table->text('footer_text')->nullable()->default(null);
			$table->string('company_admin_email')->nullable()->default(null);
			//$table->string('stripe_account')->nullable()->default(null);
			$table->string('date_site_live')->nullable()->default(null);
			$table->string('external_website_user')->nullable()->default(null);
			//$table->string('password')->nullable()->default(null);
			$table->tinyInteger('status')->nullable()->default(null);
			$table->tinyInteger('step')->nullable()->default(null);
			$table->integer('package_id')->nullable()->default(null);
			$table->string('stripe')->nullable()->default(null);
			$table->tinyInteger('site_live')->nullable()->default(null);
			$table->tinyInteger('payment_fail')->nullable()->default(null);
			$table->dateTime('fail_date')->nullable()->default(null);
			$table->integer('total_payments')->nullable()->default(null);
			$table->dateTime('order_date')->nullable()->default(null);
			$table->integer('order_type')->nullable()->default(null);
			//$table->dateTime('last_login')->nullable()->default(null);
			$table->text('privacy_statement')->nullable()->default(null);
			$table->string('timezone')->nullable()->default(null);
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
		Schema::drop('companies');
	}

}

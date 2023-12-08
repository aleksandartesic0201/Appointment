<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpertChargesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expert_charges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('expert_id')->nullable()->default(null);
			$table->integer('customer_id')->nullable()->default(null);
			$table->string('service_description')->nullable()->default(null);
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
		Schema::drop('expert_charges');
	}

}

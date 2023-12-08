<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('packages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->nullable()->default(null);
			$table->integer('billing_cycle')->nullable()->default(null);
			$table->float('cost')->nullable()->default(null);
			$table->float('one_time_cost')->nullable()->default(null);
			$table->text('description')->nullable()->default(null);
			$table->integer('no_of_experts')->nullable()->default(null);
			$table->smallInteger('status')->nullable()->default(null);
			$table->integer('sorder')->nullable()->default(null);
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
		Schema::drop('packages');
	}

}

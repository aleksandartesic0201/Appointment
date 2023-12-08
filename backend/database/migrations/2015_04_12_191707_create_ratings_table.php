<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expert_ratings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('expert_id')->nullable()->default(null);
			$table->integer('rated_by')->nullable()->default(null);
			$table->integer('appointment_id')->nullable()->default(null);
			$table->float('rating')->nullable()->default(null);
			$table->string('comment')->nullable()->default(null);
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
		Schema::drop('expert_ratings');
	}

}

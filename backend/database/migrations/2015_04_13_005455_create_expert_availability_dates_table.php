<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpertAvailabilityDatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expert_availability_dates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('expert_id')->nullable()->default(null);
			$table->date('on_date')->nullable()->default(null);
			$table->string('day')->nullable()->default(null);
			$table->tinyInteger('working')->nullable()->default(null);
			$table->tinyInteger('morning_working')->nullable()->default(null);
			$table->time('morning_start')->nullable()->default(null);
			$table->time('morning_end')->nullable()->default(null);
			$table->tinyInteger('afternoon_working')->nullable()->default(null);
			$table->time('afternoon_start')->nullable()->default(null);
			$table->time('afternoon_end')->nullable()->default(null);
			$table->tinyInteger('evening_working')->nullable()->default(null);
			$table->time('evening_start')->nullable()->default(null);
			$table->time('evening_end')->nullable()->default(null);
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
		Schema::drop('expert_availability_dates');
	}

}

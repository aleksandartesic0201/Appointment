<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpertAppointmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expert_appointments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('expert_id')->nullable()->default(null);
			$table->string('name')->nullable()->default(null);
			$table->string('description')->nullable()->default(null);
			$table->date('appointment_date')->nullable()->default(null);
			$table->date('repeat_end')->nullable()->default(null);
			$table->tinyInteger('is_repeated')->nullable()->default(null);
			$table->time('starts_at')->nullable()->default(null);
			$table->time('ends_at')->nullable()->default(null);
			$table->string('day')->nullable()->default(null);
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
		Schema::drop('expert_appointments');
	}

}

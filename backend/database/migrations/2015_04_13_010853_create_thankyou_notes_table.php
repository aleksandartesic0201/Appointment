<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThankyouNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('thankyou_notes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('expert_id')->nullable()->default(null);
			$table->integer('customer_id')->nullable()->default(null);
			$table->integer('appointment_id')->nullable()->default(null);
			$table->string('thankyou_note')->nullable()->default(null);
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
		Schema::drop('thankyou_notes');
	}

}

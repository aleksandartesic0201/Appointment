<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->nullable()->default(null);
			$table->text('title')->nullable()->default(null);
			$table->text('content')->nullable()->default(null);
			$table->text('meta_title')->nullable()->default(null);
			$table->text('meta_keywords')->nullable()->default(null);
			$table->text('meta_description')->nullable()->default(null);
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
		Schema::drop('pages');
	}

}

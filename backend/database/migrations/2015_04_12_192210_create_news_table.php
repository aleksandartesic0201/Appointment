<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_news', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('company_id')->nullable()->default(null);
			$table->string('title')->nullable()->default(null);
			$table->string('image')->nullable()->default(null);
			$table->string('youtube_url')->nullable()->default(null);
			$table->string('sound_cloud_url')->nullable()->default(null);
			$table->enum('type',['image','sound','youtube'])->nullable()->default(null);
			$table->string('short_description')->nullable()->default(null);
			$table->dateTime('published_at')->nullable()->default(null);
			$table->tinyInteger('share_on_twitter')->nullable()->default(null);
			$table->tinyInteger('share_on_facebook')->nullable()->default(null);
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
		Schema::drop('company_news');
	}

}

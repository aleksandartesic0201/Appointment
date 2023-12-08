<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpertPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expert_permissions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('expert_id')->nullable()->default(null);
			$table->tinyInteger('view_financial_dashboard')->nullable()->default(null);
			$table->tinyInteger('add_services')->nullable()->default(null);
			$table->tinyInteger('can_charge_customer')->nullable()->default(null);
			$table->tinyInteger('view_customers')->nullable()->default(null);
			$table->tinyInteger('view_team_members')->nullable()->default(null);
			$table->tinyInteger('custom_appointments')->nullable()->default(null);
			$table->tinyInteger('mobile_charge')->nullable()->default(null);
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
		Schema::drop('expert_permissions');
	}

}

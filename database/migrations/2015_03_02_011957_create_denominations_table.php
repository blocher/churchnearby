<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDenominationsTable extends Migration {

	public function up()
	{
		Schema::create('denominations', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->string('name', 256);
			$table->string('url', 255);
			$table->string('region_name')->nullable()->index();
		});
	}

	public function down()
	{
		Schema::drop('denominations');
	}
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRegionsTable extends Migration {

	public function up()
	{
		Schema::create('regions', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->string('long_name')->index();
			$table->string('short_name')->index();
			$table->string('url')->nullable();
			$table->integer('denomination')->unsigned()->index();
		});
	}

	public function down()
	{
		Schema::drop('regions');
	}
}
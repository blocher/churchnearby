<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChurchesTable extends Migration {

	public function up()
	{
		Schema::create('churches', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->string('externalid');
			$table->integer('region')->unsigned();
			$table->string('leader')->nullable();
			$table->float('latitude', 5,5)->index();
			$table->float('longitude', 5,5)->index();
			$table->string('name', 256)->index();
			$table->string('url', 255);
			$table->string('address')->nullable();
			$table->string('city')->nullable();
			$table->string('state')->nullable();
			$table->string('zip')->nullable();
			$table->string('email')->nullable();
			$table->string('phone')->nullable();
			$table->string('twitter')->nullable();
			$table->string('facebook')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('churches');
	}
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('churches', function(Blueprint $table) {
			$table->foreign('region')->references('id')->on('regions')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('regions', function(Blueprint $table) {
			$table->foreign('denomination')->references('id')->on('denominations')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
	}

	public function down()
	{
		Schema::table('churches', function(Blueprint $table) {
			$table->dropForeign('churches_region_foreign');
		});
		Schema::table('regions', function(Blueprint $table) {
			$table->dropForeign('regions_denomination_foreign');
		});
	}
}
<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DenominationsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		 DB::table('denominations')->delete();
        
         $denomination = new \App\Models\Denomination();
      	 $denomination->name = 'The Episcopal Church';
      	 $denomination->slug = 'episcopal';
      	 $denomination->url = 'http://www.episcopalchurch.org/';
      	 $denomination->region_name ='diocese';
      	 $denomination->region_name_plural = 'diocess';
      	 $denomination->save();

	}

}

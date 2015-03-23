<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Denomination extends Model {

	protected $table = 'denominations';
	public $timestamps = true;

	use SoftDeletes;

	protected $dates = ['deleted_at'];
	protected $fillable = array('name', 'url', 'region_name');

	public function regions()
	{
		return $this->hasMany('App\Models\Region', 'denomination');
	}

}
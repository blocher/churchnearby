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
		return $this->hasMany('App\Models\Region', 'denomination_id','id');
	}

	public function churches()
    {
        return $this->hasManyThrough('App\Models\Church', 'App\Models\Region', 'denomination_id', 'region_id');
    }

}
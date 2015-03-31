<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model {

	protected $table = 'regions';
	public $timestamps = true;

	use SoftDeletes;

	protected $dates = ['deleted_at'];
	protected $fillable = array('long_name', 'short_name', 'url', 'denomination');

	public function denomination()
	{
		return $this->belongsTo('App\Models\Denomination', 'denomination_id','id');
	}

	public function churches()
	{
		return $this->hasMany('App\Models\Churches', 'region_id','id');
	}

}
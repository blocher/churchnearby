<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Church extends Model {

	protected $table = 'churches';
	public $timestamps = true;

	use SoftDeletes;

	protected $dates = ['deleted_at'];
	protected $fillable = array('externalid', 'leader', 'latitude', 'longitude', 'name', 'url', 'address', 'city', 'state', 'zip', 'email', 'phone', 'twitter', 'facebook');

	public function region()
	{
		return $this->belongsTo('App\Models\Region','region');
	}

	public function denomination()
	{
		$region = $this->region;
		$denomination = Region::find($region)->denomination;
		return Denomination::find($denomination);
	}

	public function denominationSlug()
	{
		return $this->denomination()->slug;
	}

}
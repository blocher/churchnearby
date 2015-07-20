<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Input;

class Church extends Model {

	protected $table = 'churches';
	public $timestamps = true;

	use SoftDeletes;

	protected $dates = ['deleted_at'];
	protected $fillable = array('externalid', 'leader', 'latitude', 'longitude', 'name', 'url', 'address', 'city', 'state', 'zip', 'email', 'phone', 'twitter', 'facebook');

	public function region()
	{
		return $this->belongsTo('\App\Models\Region','region_id','id');
	}

	public static function nearbyChurches($latitude=38.813832399999995,$longitude=-77.1096706, $count=30, $denomination = '') {

	    $latitude = floatval($latitude);
	    $longitude = floatval($longitude);
	    $count = intval($count);

	    $circles = array(); 
	    //
	    $distance_unit = 69;
	    $radius = 30000;

	    $churches = 
	    \App\Models\Church::select(DB::raw('*, round(
	    		' . $distance_unit . "
	             * DEGREES(ACOS(COS(RADIANS(" . $latitude . "))
	             * COS(RADIANS(latitude))
	             * COS(RADIANS(" . $longitude . ") - RADIANS(longitude))
	             + SIN(RADIANS(" . $latitude . "))
	             * SIN(RADIANS(latitude))))
	             ,2) AS distance_in_miles"))

	      ->where('latitude','>',DB::raw($latitude . "  - (" . $radius . " / " . $distance_unit . ")"))
	      ->where('latitude','<',DB::raw($latitude . "  + (" . $radius . " / " . $distance_unit . ")"))

	      ->where('longitude','>',DB::raw($longitude . " - (" . $radius . " / (" . $distance_unit . " * COS(RADIANS(" . $latitude . "))))"))
	      ->where('longitude','<',DB::raw($longitude . " + (" . $radius . " / (" . $distance_unit . " * COS(RADIANS(" . $latitude . "))))"))
	      
	      ;

	      if (!empty($denomination)) {
	        $churches = 
	          $churches->whereHas('region', function($q) use ($denomination)
	          {
	              $q->where('denomination_id', $denomination);
	          });
	      }

	      $churches = $churches
	      ->with('region')
	      ->with('region.denomination')

	      ->orderBy('distance_in_miles','ASC')
	      ->take($count)
	      ->get();
	    ;

	    return $churches;

	}

}
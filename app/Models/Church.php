<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Input;

class Church extends Model {

	protected $table = 'churches';
	public $timestamps = true;

	public $full_address;
	public $full_address_encoded;

	use SoftDeletes;

	protected $dates = ['deleted_at'];
	protected $fillable = array('externalid', 'leader', 'latitude', 'longitude', 'name', 'url', 'address', 'city', 'state', 'zip', 'email', 'phone', 'twitter', 'facebook');

	public function toArray()
    {
        $array = parent::toArray();
        $array['full_address'] =  $this->address . ' ' . $this->city . ', ' . $this->state . ' ' . $this->zip;
        $array['full_address_encoded'] = $this->full_address_encoded = urlencode(str_replace('&', '', $array['full_address']));
        return $array;

    }

	public function region()
	{
		return $this->belongsTo('\App\Models\Region','region_id','id');
	}

	public static function nearbyChurches($latitude=38.813832399999995,$longitude=-77.1096706, $count=30, $denominations = '') {

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

	      if (!empty($denominations)) {

	      	//can accept a string of one or multiple comma separate denomiations, seperated by commas
	  		// Or, if it's already an array, let's move on
	      	if (!is_array($denominations)) {
	      		$denominations = explode(',',$denominations);
	      	}

	      	//accepts denomiation objects, OR denomination IDs, OR denomination slug, BUT coverts all to IDs here
	      	$ids = array();
	      	foreach ($denominations as $denomination) {

	      		if (is_object($denomination) && isset($denomination->id)) {
		      		$ids[] = $denomination->id;
		      	}  else if (is_numeric($denomination)) {
		      		$ids[] = $denomination;
		      	} else  {
		      		$denomination = \App\Models\Denomination::where('slug',$denomination)->first();
		      		if (is_object($denomination) && isset($denomination->id)) {
		      			$ids[] = $denomination->id;
		      		}
		      	}
	      	}

	        $churches = 
	          $churches->whereHas('region', function($q) use ($ids)
	          {
	              $q->whereIn('denomination_id', $ids);
	          });

	        $denominations = \App\Models\Denomination::whereIn('id',$ids)->get();

	      }

	      $churches = $churches
	      ->with('region')
	      ->with('region.denomination')

	      ->orderBy('distance_in_miles','ASC')
	      ->take($count)
	      ->get();
	    ;

	    $result = [];
	    $result['churches'] = $churches;
	    $result['denominations'] = $denominations;

	    return $result;

	}

}
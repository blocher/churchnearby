<?php namespace App\Http\Controllers;

use DB;
use Input;

class HomeController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	public function getDiocese($churches) {

		$region = \App\Models\Region::find($churches[0]->region);
		$bestguess = $region->long_name;

		return $bestguess;
		$possibilities = array();
		foreach ($churches as $church) {
			$possibilities = $church->region->name;
		}
		$possibilities = array_unique($possibilities);



	}

	public function apiNearbyChurches() {
		
		$latitude = Input::get('latitude',38.813832399999995);
		$longitude = Input::get('longitude',-77.1096706);
		$count = Input::get('count',30);

		$result = array();
		$result['churches'] = $this->nearbyChurches($latitude, $longitude, $count);
		$result['latitude'] = $latitude;
		$result['longitude'] = $longitude;
		$result['diocese'] = $this->getDiocese( $result['churches'] );

		return $result;

	}

	public function apiNearbyChurchesView() {
		
		$result = $this->apiNearbyChurches();
		return view('slices/churchlist')
			->with('churches',$result['churches'])
			->with('longitude',$result['longitude'])
			->with('latitude',$result['latitude'])
			->with('diocese',$result['diocese'])
			;

	}

	public function nearbyChurches($latitude=38.813832399999995,$longitude=-77.1096706, $count=30) {

		$latitude = floatval($latitude);
		$longitude = floatval($longitude);
		$count = intval($count);

		$circles = array(); 
		//
		$distance_unit = 69;
		$radius = 30000;

		$churches = 
		\App\Models\Church::select(DB::raw('*, ' . $distance_unit . "
             * DEGREES(ACOS(COS(RADIANS(" . $latitude . "))
             * COS(RADIANS(latitude))
             * COS(RADIANS(" . $longitude . ") - RADIANS(longitude))
             + SIN(RADIANS(" . $latitude . "))
             * SIN(RADIANS(latitude)))) AS distance_in_miles"))

			->where('latitude','>',DB::raw($latitude . "  - (" . $radius . " / " . $distance_unit . ")"))
			->where('latitude','<',DB::raw($latitude . "  + (" . $radius . " / " . $distance_unit . ")"))

			->where('longitude','>',DB::raw($longitude . " - (" . $radius . " / (" . $distance_unit . " * COS(RADIANS(" . $latitude . "))))"))
			->where('longitude','<',DB::raw($longitude . " + (" . $radius . " / (" . $distance_unit . " * COS(RADIANS(" . $latitude . "))))"))
			
		//	->with('region')
		//	->with('region.denomination')

			->orderBy('distance_in_miles','ASC')
			->take($count)
			->get();

		return $churches;

	}



	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('home');
	}

}

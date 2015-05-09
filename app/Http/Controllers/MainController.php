<?php namespace App\Http\Controllers;

use DB;
use Input;

class MainController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	public function getDiocese($churches) {

		return '';

		$region = \App\Models\Region::find($churches[0]->region);
		$bestguess = $region->long_name;

		return $bestguess;
		$possibilities = array();
		foreach ($churches as $church) {
			$possibilities = $church->region->name;
		}
		$possibilities = array_unique($possibilities);



	}

	public function nearbyChurches() {
		
		$latitude = floatval(Input::get('latitude',38.813832399999995));
		$longitude = floatval(Input::get('longitude',-77.1096706));
		$denomination = Input::get('denomination','');
		$count = Input::get('count',30);

		$churches = $this->nearbyChurchList($latitude, $longitude, $count, $denomination);

		$result = array();
		$result['latitude'] = $latitude;
		$result['longitude'] = $longitude;
		$result['denomination'] = $denomination;
		$result['count'] = count( $churches );
		$result['churches'] = $churches;
		
		//$result['diocese'] = $this->getDiocese( $result['churches'] );

		return $result;

	}

	public function apiNearbyChurchesView() {
		
		$result = $this->apiNearbyChurches();
		return view('slices/churchlist')
			->with('churches',$result['churches'])
			->with('longitude',$result['longitude'])
			->with('latitude',$result['latitude'])
			//->with('diocese',$result['diocese'])
			;

	}


	//so, let's move this to the model soon, ok, ben?
	public function nearbyChurchList($latitude=38.813832399999995,$longitude=-77.1096706, $count=30, $denomination = '') {

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



	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$denominations = \App\Models\Denomination::orderBy('name','ASC')->get();
		return view('home')
			->with('denominations',$denominations);
	}

}

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

		$churches = \App\Models\Church::nearbyChurches($latitude, $longitude, $count, $denomination);

		$result = array();
		$result['latitude'] = $latitude;
		$result['longitude'] = $longitude;
		$result['denomination'] = $denomination;
		$result['count'] = count( $churches );
		$result['churches'] = $churches;
		
		//$result['diocese'] = $this->getDiocese( $result['churches'] );

		return $result;

	}

	/* TODO: Let's try to move this into Angular eventually */
	public function NearbyChurchesView() {
		
		$result = $this->NearbyChurches();
		return view('slices/churchlist')
			->with('churches',$result['churches'])
			->with('longitude',$result['longitude'])
			->with('latitude',$result['latitude'])
			//->with('diocese',$result['diocese'])
			;
	}


}

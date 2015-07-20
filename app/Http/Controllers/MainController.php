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

	private function geocode($address) {

		$results = array();
		if (empty($address)) {
			$results['status'] = 'error';
			$results['error'] = 'You must provide an address.';
			return $results;
		}
		
		try {
			//TODO: move this to config file
		    $key = '13079da9c1d29c525d3d54115d37c9d9a59a2d5';
			$data = \Geocodio::get($address, $key);

			if (!$data->response->results || !is_array($data->response->results) || $data->response->results[0]->accuracy<.5) {
				$results['status'] = 'error';
				$results['error'] = 'We were not able to determine you location from this address.  Please check the address and try again.';
				return $results;
			} else {

				$results = array();
				$results['status'] = 'ok';
				$results['address'] = $data->response->results[0]->formatted_address;
				$results['latitude'] = $data->response->results[0]->location->lat;
				$results['longitude'] = $data->response->results[0]->location->lng;
				$results['accuracy'] = $data->response->results[0]->accuracy;
				return $results;
			}
			

		} catch (BadResponseException $e) {
			$results['status'] = 'error';
			$results['error'] = 'There was an unknown error.  Please try again.';
			return $results;
		}

		return $results;

	}

	public function nearbyChurches() {
	
		$latitude = $longitude = '';

		if (Input::get('address')) {
			$points = $this->geocode(Input::get('address'));
			if ($points) {
				$latitude = $points['latitude'];
				$longitude = $points['longitude'];
			}
		} else {
			$latitude = floatval(Input::get('latitude'));
			$longitude = floatval(Input::get('longitude'));
		}

		if (empty($latitude) || empty($longitude)) {
			return;
		}
		
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
	public function nearbyChurchesView() {
		
		$result = $this->NearbyChurches();
		return view('slices/churchlist')
			->with('churches',$result['churches'])
			->with('longitude',$result['longitude'])
			->with('latitude',$result['latitude'])
			//->with('diocese',$result['diocese'])
			;
	}


}

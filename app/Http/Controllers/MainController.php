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

	public function getRegion($churches) {

		$num = count($churches)<5 ? count($churches) : 5;
		
		$region = array();
		$region['closest_match'] = $churches[0]->region->long_name;

		for ($i=0;$i<$num;$i++) {
			$regions[] = $churches[$i]->region->long_name;
		}

		$regions = array_unique($regions);
		$region['possible_matches'] = $regions;

	    if (count($regions)>1) {
	    	$region['message'] = 'This location is near the border of the following: ' . implode($regions,', ') . '.';
	    } else if (count($regions)>0) {
	    	$region['message'] = 'This location is in the ' . $region['closest_match'] . '.';
	    } else {
	    	$region = array();
	    }
	    return $region;

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
		
		$denominations = Input::get('denominations','');
		if (!empty($denominations)) {
			$denominations = explode(',',$denominations);
		}
		
		$count = Input::get('count',30);

		$result = \App\Models\Church::nearbyChurches($latitude, $longitude, $count, $denominations);

		$result['latitude'] = $latitude;
		$result['longitude'] = $longitude;
		$result['count'] = count( $result['churches'] );

		$result['region'] = empty ($denomination) ? '' : $this->getRegion( $result['churches'] );

		return $result;

	}

	public function denominations() {

		$denominations = $churches = \App\Models\Denomination::orderBy('tag_name')->get();
		$result = [];
		$result['denominations'] = $denominations;
		return $result;
	}
}

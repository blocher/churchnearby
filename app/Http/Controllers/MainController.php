<?php namespace App\Http\Controllers;

use DB;

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
			

		} catch (\Exception $e) {
			$results['status'] = 'error';
			$results['error'] = 'There was an error finding the specified address.  Please try a diffrent address';
			return $results;
		}

		return $results;

	}

	public function nearbyChurches() {
	
		$latitude = $longitude = '';

		if (request()->get('address')) {
			$address = request()->get('address');
			$points = $this->geocode(request()->get('address'));
			if ($points['status']!='ok') {
				return $points;
			}
			if ($points) {
				$latitude = $points['latitude'];
				$longitude = $points['longitude'];
			}
		} else {
			$address = '';
			$latitude = floatval(request()->get('latitude'));
			$longitude = floatval(request()->get('longitude'));
		}

		if (empty($latitude) || empty($longitude)) {
			return;
		}
		
		$denominations = request()->get('denominations','');
		if (!empty($denominations)) {
			$denominations = explode(',',$denominations);
		}
		
		$count = request()->get('count',30);

		$result = \App\Models\Church::nearbyChurches($latitude, $longitude, $count, $denominations);

		$result['address'] = $address;
		$result['latitude'] = $latitude;
		$result['longitude'] = $longitude;
		$result['count'] = count( $result['churches'] );

		$result['region'] = empty ($denomination) ? '' : $this->getRegion( $result['churches'] );
		$result['status'] = 'ok';
		return $result;

	}

	public function church() {
	
		$result['status'] = 'ok';

		if (request()->get('id')) {
			$church = \App\Models\Church::with('region')->with('region.denomination')->find(request()->get('id'));
			if ($church!=false) {
				$result['church'] = $church;
			} else {
				$result['status'] = 'error';
				$result['error'] = 'No church with supplied ID found.';
			}
		} else {
			$result['status'] = 'error';
			$result['error'] = 'You must supply an ID.';
			
		}
		return $result;

	}

	public function denominations() {

		$denominations = $churches = \App\Models\Denomination::orderBy('tag_name')->get();
		$result = [];
		$result['denominations'] = $denominations;
		return $result;
	}

	public function randomPic($dir='')
	{
		$dir = public_path().'/img/cover';
	    $files = glob($dir . '/*.*');
	    $file = array_rand($files);
	    $file = $files[$file];
	    $file = explode('/',$file);
	    $file = array_pop($file);

	    return ['photo_url'=>asset('img/cover/'.$file)];
	}
}

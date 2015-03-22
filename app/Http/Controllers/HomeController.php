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

	public function apiNearbyChurches() {
		
		$latitude = Input::get('latitude',38.813832399999995);
		$longitude = Input::get('longitude',-77.1096706);
		$count = Input::get('count',30);

		$result = array();
		$result['churches'] = $this->nearbyChurches($latitude, $longitude, $count);
		$result['latitude'] = $latitude;
		$result['longitude'] = $longitude;

		return $result;

	}

	public function apiNearbyChurchesView() {
		
		$result = $this->apiNearbyChurches();
		return view('slices/churchlist')
			->with('churches',$result['churches'])
			->with('longitude',$result['longitude'])
			->with('latitude',$result['latitude'])
			;

	}

	public function nearbyChurches($latitude=38.813832399999995,$longitude=-77.1096706, $count=30) {

		$latitude = floatval($latitude);
		$longitude = floatval($longitude);
		$count = intval($count);

		$circles = array(); 

		$sql =
		'
			SELECT  c.*,
			        p.distance_unit
			                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
			                 * COS(RADIANS(c.latitude))
			                 * COS(RADIANS(p.longpoint) - RADIANS(c.longitude))
			                 + SIN(RADIANS(p.latpoint))
			                 * SIN(RADIANS(c.latitude)))) AS distance_in_miles
			  FROM churches AS c
			  JOIN (   /* these are the query parameters */
			        SELECT  ' . $latitude .'  AS latpoint,' . $longitude . ' AS longpoint,
			                30000.0 AS radius,      69.0 AS distance_unit
			    ) AS p
			  WHERE c.latitude
			     BETWEEN p.latpoint  - (p.radius / p.distance_unit)
			         AND p.latpoint  + (p.radius / p.distance_unit)
			    AND c.longitude
			     BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
			         AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
			  ORDER BY distance_in_miles ASC
			  LIMIT ' . $count . ';
		';

		//why won't bindings work?  Poreteciting with intval and floatval above instead
		//$results = DB::select($sql,['latitude'=>$latitude,'longitude'=>$longitude,'count'=>$count]);
		$results = DB::select($sql);
		return $results;

	}



	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('home');
		$churches = $this->nearbyChurches();
		echo '<div id="demo"></div>';
		foreach ($churches as $church) {
			echo '<h3>' . $church->name. '</h3>';
			echo '<p>' . $church->distance_in_miles .' miles away</p>';
		}
		
		echo '

			<script>
				var x = document.getElementById("demo");
				function getLocation() {
				    if (navigator.geolocation) {
				        navigator.geolocation.getCurrentPosition(showPosition);
				    } else {
				        x.innerHTML = "Geolocation is not supported by this browser.";
				    }
				}
				function showPosition(position) {
				    x.innerHTML = "Latitude: " + position.coords.latitude + 
				    "<br>Longitude: " + position.coords.longitude; 
				}
			getLocation();
			</script>
			

		';
		
	}

}

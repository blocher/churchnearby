<?php namespace App\Http\Controllers;

use DB;

class HomeController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	function nearbyChurches($latitude=38.804836,$longitude=-77.046921) {

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
			        SELECT  ' . $latitude .'  AS latpoint, ' . $longitude . ' AS longpoint,
			                30000.0 AS radius,      69.0 AS distance_unit
			    ) AS p
			  WHERE c.latitude
			     BETWEEN p.latpoint  - (p.radius / p.distance_unit)
			         AND p.latpoint  + (p.radius / p.distance_unit)
			    AND c.longitude
			     BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
			         AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
			  ORDER BY distance_in_miles ASC
			  LIMIT 15
		';

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
		$churches = $this->nearbyChurches();
		foreach ($churches as $church) {
			echo '<h3>' . $church->name. '</h3>';
			echo '<p>' . $church->distance_in_miles .' miles away</p>';
		}
		
	}

}

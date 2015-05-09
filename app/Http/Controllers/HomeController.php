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

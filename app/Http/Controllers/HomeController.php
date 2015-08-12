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
		return view('home')
			->with('cover_photo',self::random_pic());
		;
	}


	public static function random_pic($dir='')
	{
		$dir = public_path().'/img/cover';
	    $files = glob($dir . '/*.*');
	    $file = array_rand($files);
	    $file = $files[$file];
	    $file = explode('/',$file);
	    $file = array_pop($file);
	    return $file;
	}

	
}

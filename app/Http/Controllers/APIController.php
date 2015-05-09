<?php namespace App\Http\Controllers;

use DB;
use Input;

class APIController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	public function json($controller,$function) {

		$controller_name = ucfirst($controller . 'Controller');
		$controller_name = 'App\Http\Controllers\\' . $controller_name;
		$controller = new $controller_name;

		$callback = Input::get('callback');

		if (empty($callback)) {
			return response()->json($controller->$function());
		} else {
			return response()->json($controller->$function())->setCallback($callback);;
		}

	}

	public function jsonDefaultController($function) {
		return $this->json('main',$function);
	}

	public function jsonpDefaultController($function) {
		return $this->jsonp('main',$function);
	}

}

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

	public function json($controller,$method) {

		$controller_name = ucfirst($controller . 'Controller');
		$controller_name = 'App\Http\Controllers\\' . $controller_name;

		$result = [];
	

		if(!class_exists($controller_name)){

			$status = 'error';

			$error['id'] = 1;
			$error['status'] = 404;
			$error['code'] = 'controller_not_found';
			$error['details'] = 'Endpoint not found: The controller you specified does not exist.';

			$result['status'] = 'error';
			$result['errors'] = array($error);

		} else if (!method_exists ( $controller_name , $method )) {

			$status = 'error';

			$error['id'] = 2;
			$error['status'] = 404;
			$error['code'] = 'method_not_found';
			$error['details'] = 'Endpoint not found: The method you specified does not exist.';

			$result['status'] = 'error';
			$result['errors'] = array($error);

		} else {

			$controller = new $controller_name();
			$result = $controller->$method();
			$result['status'] = 'ok';

		}

		$callback = Input::get('callback');

		if (empty($callback)) {
			return response()->json($result);
		} else {
			return response()->json($result)->setCallback($callback);
		}

	}

	public function jsonDefaultController($method) {
		return $this->json('main',$method);
	}

	public function jsonpDefaultController($method) {
		return $this->jsonp('main',$method);
	}

}

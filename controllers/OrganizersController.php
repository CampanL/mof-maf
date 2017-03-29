<?php 

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application as App;
use Models\Department as Department;

class OrganizersController extends Controllers{

	public function __construct( App $app ){

		parent::__construct($app,'organizers');

		$this->access = ['admin'];
	}
	public function save(Request $request){
		$this->utils->accessVerif($this->access);

		$request = $request->request->all();
		
		if ($request["password"]) $request["password"] = sha1($request["password"]);

		$request['id_role'] = 2;
		
		$this->model->create($request);
		
		return $this->app->redirect('/admin/organizers');
	}

	public function update(Request $request){
		$this->utils->accessVerif($this->access);

		$request = $request->request->all();
		
		if ($request["password"]) $request["password"] = sha1($request["password"]);
		
		foreach ($request as $key => $value) {
			if ($value == '') {
				unset($request[$key]);
			}
		}

		$this->model->update($request);

		$this->app['session']->getFlashBag()->add('alert', 'Utilisateur modifié');
		return $this->app->redirect('/admin/organizers');
	}
}
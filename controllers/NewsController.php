<?php 

namespace Controllers;

use Silex\Application as App;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controllers{

	public function __construct( App $app ){

		parent::__construct($app,'news');

		$this->access = ['admin'];
	}

	public function save(Request $request){
		$this->utils->accessVerif($this->access);

		$request = $request->request->all();

		move_uploaded_file($_FILES["img"]["tmp_name"], 'assets/images/'.$_FILES["img"]["name"]);

		$request['img'] = '/assets/images/'.$_FILES["img"]["name"];
		
		$request['published_at'] = date("Y-m-d H:i:s");

		$this->model->create($request);
		
		return $this->app->redirect('/'.'admin/'.$this->table);

	}

	public function update(Request $request){
		$this->utils->accessVerif($this->access);

		$request = $request->request->all();
		
		foreach ($request as $key => $value) {
			if ($value == '') {
				unset($request[$key]);
			}
		}

		if ($_FILES["img"]["tmp_name"] != '') {
			move_uploaded_file($_FILES["img"]["tmp_name"], 'assets/images/'.$_FILES["img"]["name"]);

			$request['img'] = '/assets/images/'.$_FILES["img"]["name"];
		}

		$this->model->update($request);

		$this->app['session']->getFlashBag()->add('alert', 'Utilisateur modifié');
		return $this->app->redirect('/'.'admin/'.$this->table);
	}
}
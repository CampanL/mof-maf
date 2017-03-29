<?php 

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application as App;

class ContractsController extends Controllers{

	public function __construct( App $app ){

		parent::__construct($app,'contracts');

		$this->access = [];
	}

	public function toSession(Request $request){
		$data = $request->request->all();

		$session = $this->app['session']->get('registration');
	
		$session['contract'] = $data;

		$this->app['session']->set('registration',$session);
		$this->app['session']->set('step',4);

		return $this->app->redirect('/candidate/create');
	}
}
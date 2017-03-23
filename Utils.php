<?php 

namespace Utils;

use Silex\Application as App;

class Utils{
		
	private $app;

	function __construct(App $app){
		$this->app = $app;
	}
	
	public function accessVerif(){
		if (empty($this->app['session']->get('user'))) {
			$this->app['session']->getFlashBag()->add('connectError','Veuillez vous connecter');
			header('Location:'.\URL.'/admin');
			exit();
		}
	}	
}
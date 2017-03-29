<?php 

namespace Utils;

use Silex\Application as App;

class Utils{
		
	private $app;

	function __construct(App $app){
		$this->app = $app;
	}
	
	public function accessVerif($users=[]){

		if (empty($users)) return true;

		$session = $this->app['session']->get('user');

		$msg = 'Veuillez vous connecter';

		//On verifie que le compte actuel possede le role approprié.
		foreach ($users as $key => $value) {
			if ($session['role'] == $value) {
				return true; // Si oui on sort de la fonction
			}

			$msg = 'Vous n\'avez pas les droits d\'acceder a cette page. Connectez-vous avec les droits';
		}
		
		$this->app['session']->getFlashBag()->add('connectError',$msg);
		header('Location:'.\URL.'/admin');// Sinon on redirige
		exit();
	}	
}
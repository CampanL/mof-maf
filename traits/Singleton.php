<?php 
	namespace Traits;

	trait Singleton {

		function verifConnect(){
			//Verif si un admin est connecté

			if (empty($this->app['session']->get('user'))) {
				$this->app['session']->getFlashBag()->add('connectError','Veuillez vous connecter');
				return $this->app->redirect('/admin');
			}			
		}
	}
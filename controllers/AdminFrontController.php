<?php 

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application as App;
use Models\Admins as Admins;
use Symfony\Component\Validator\Constraints as Assert;
use Utils\Utils as Utils;

class AdminFrontController{

	private $admins;
	private $app;
	private $utils;

	private $constraint;

	public function __construct( App $app ){

		$this->admins = new Admins($app);
		$this->app = $app;

		$this->utils = new Utils($app);

		$this->constraint = new Assert\Collection(array(
		    'mail' => [
		    	new Assert\NotBlank(['message' => "Le champ email doit etre renseigné."]),
		    	new Assert\Email(array('message' => "Ceci n'est pas un format d'email valide.")),
		    ],
		    'password' => new Assert\NotBlank(['message' => "Le champ mot de passe doit etre renseigné."])
		));
	}

	//Renvoie le formulaire de connection
	public function connectForm(){
		return $this->app['twig']->render('fronts/admin/connect.twig');
	}

	//Connect l'admin ou la renvoie sur la page d'accueil en cas d'erreur
	public function connect(Request $request){
		$redirect = '/admin/home';
		
		$errors = $this->app['validator']->validate($request->request->all(), $this->constraint);

		if (count($errors)>0) {
			$errorsMessage = [];
			foreach ($errors as $error) {
				$errorsMessage[] = $error->getMessage();
			}

			$this->app['session']->getFlashBag()->add('errors', $errorsMessage);
			return $this->app->redirect('/admin');
		}

		$data =  $this->admins->connect( $request->request->all());
		
		if (empty($data)) {
			$redirect = '/admin';
			$this->app['session']->getFlashBag()->add('not_found', 'Couple mail / mot de passe non reconnue');
		}

		$this->app['session']->set('user',['username' => $data->mail]);

		return $this->app->redirect($redirect);
	}

	//Sert la page d'accueil des admins
	public function index(){
		$this->utils->accessVerif();

		return $this->app['twig']->render('fronts/admin/home.twig');
	}
}
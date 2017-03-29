<?php 

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application as App;
use Symfony\Component\Validator\Constraints as Assert;
use Models\Organizers as Organizers;

class BackOfficeController extends Controllers{

	public function __construct( App $app ){

		parent::__construct($app,'admins');

		$this->constraint = new Assert\Collection(array(
		    'mail' => [
		    	new Assert\NotBlank(['message' => "Le champ email doit etre renseigné."]),
		    	new Assert\Email(array('message' => "Ceci n'est pas un format d'email valide.")),
		    ],
		    'password' => new Assert\NotBlank(['message' => "Le champ mot de passe doit etre renseigné."])
		));
	}

	//Renvoie le formulaire de connection d'admin
	public function adminConnectForm(){
		return $this->app['twig']->render('fronts/backOffice/admins/connect.twig');
	}

	//Renvoie le formulaire d'organisateur
	public function organizerConnectForm(){
		return $this->app['twig']->render('fronts/backOffice/organizers/connect.twig');
	}


	//Connect l'admin ou la renvoie sur la page d'accueil en cas d'erreur
	public function adminConnect(Request $request){
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

		$request = $request->request->all();

		$request["password"] = sha1($request["password"]);

		$data =  $this->model->connect( $request );
		
		
		if (empty($data)) {
			$redirect = '/admin';
			$this->app['session']->getFlashBag()->add('not_found', 'Couple mail / mot de passe non reconnue');
		}
		
		$this->app['session']->set('user',[
			'userName' => $data->name,
			'userSecondName' => $data->second_name,
			'role' => $data->role,
		]);

		return $this->app->redirect($redirect);
	}

	//Sert la page d'accueil des admins
	public function index(){
		$this->utils->accessVerif(['admin']);

		return $this->app['twig']->render('fronts/backOffice/home.twig');
	}

	//deconenct l'utilisateur
	public function deconnexion(){
		$this->app['session']->getFlashBag()->add('connectError', 'Vous êtes déconnecté');

		$a = $this->app['session']->get('user');

		$this->app['session']->set('user', null);	

		return $this->app->redirect('/'.$a['role']);
	}

	//Connect l'admin ou la renvoie sur la page d'accueil en cas d'erreur
	public function organizerConnect(Request $request){
		$redirect = '/admin/candidates';
		
		$errors = $this->app['validator']->validate($request->request->all(), $this->constraint);

		if (count($errors)>0) {
			$errorsMessage = [];
			foreach ($errors as $error) {
				$errorsMessage[] = $error->getMessage();
			}

			$this->app['session']->getFlashBag()->add('errors', $errorsMessage);
			return $this->app->redirect('/organizer');
		}

		$request = $request->request->all();

		$request["password"] = sha1($request["password"]);

		$model = new Organizers($this->app);

		$data = $model->connect( $request );
		
		
		if (empty($data)) {
			$redirect = '/organizer';
			$this->app['session']->getFlashBag()->add('not_found', 'Couple mail / mot de passe non reconnue');
		}
		
		$this->app['session']->set('user',[
			'userName' => $data->name,
			'userSecondName' => $data->second_name,
			'role' => $data->role,
		]);

		return $this->app->redirect($redirect);
	}
}
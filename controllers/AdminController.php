<?php 

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application as App;
use Models\Admins as Admins;
use Symfony\Component\Validator\Constraints as Assert;
use Utils\Utils as Utils;


class AdminController{
	private $admins;
	private $app;
	private $utils;

	private $constraint;


	public function __construct( App $app ){

		$this->admins = new Admins($app);
		$this->app = ($app);
		$this->utils = new Utils($app);
	}

	/**
	 * Affiche tout les administrateurs de la BDD
	 * @return [view] Retourne la vue associé avec les datas
	 */
	public function index(){
		$this->utils->accessVerif();

		$admins = $this->admins->index();	

		return $this->app['twig']->render('fronts/admin/administrateurs.twig',['data'=>$admins]);
	}

	/**
	 * Sert le formulaire de création d'un admin
	 * @return [view] Retourne la vue associé
	 */
	public function create(){
		$this->utils->accessVerif();

		return $this->app['twig']->render('fronts/admin/creer.twig');
	}

	/**
	 * Enregistre l'utilisateur dans la base de donnée.
	 * @param  [Request $request] Donnée issus du formulaire
	 * @return [view]			  Retourne la vue associés² avec les datas
	 */
	public function save(Request $request){
		$this->utils->accessVerif();

		$response = $this->admins->create($request->request->all());

		//On verifie si le mail est déja enregistrée
		if (!$response) {
			//Si oui affiche une erreur et on redirectionne vers la page de création
			$this->app['session']->getFlashBag()->add('errors', 'Mail déjà enregistré');
			return $this->app->redirect('/admin/administrateurs/create');
		}
		
		//On redirige vers la page de gestion des administrateurs
		return $this->app->redirect('/admin/administrateurs');
	}

	/**
	 * Supprime un utilisateur de la base
	 * @param  [Request $request Donnée transmise dans la requete
	 * @return [view]           Redirige vers la page de gestiopn des admins avec un message de succes
	 */
	public function delete(Request $request){
		$this->utils->accessVerif();

		$id = $request->attributes->all()['id'];

		$this->admins->delete($id);

		$this->app['session']->getFlashBag()->add('alert', 'Utilisateur supprimé');
		return $this->app->redirect('/admin/administrateurs');
	}

	/**
	 * Sert le formulaire de mise a jour des admins
	 * @param  [int] $id Id de l'admin a afficher
	 * @return [view]     Retoune la vue avec les datas associées
	 */
	public function edit($id){
		$this->utils->accessVerif();

		$user = $this->admins->read($id);

		return $this->app['twig']->render('fronts/admin/edit.twig',['data'=>$user]);
	}

	/**
	 * Enregistré les modifications dans la base de donnée
	 * @param  [Request $request] Donnée issus du formulaire
	 * @return [view]             Redirige vers la page d'administration des admins avec un message
	 */
	public function update(Request $request){
		$this->utils->accessVerif();

		$this->admins->update($request->request->all());

		$this->app['session']->getFlashBag()->add('alert', 'Utilisateur modifié');
		return $this->app->redirect('/admin/administrateurs');
	}
}
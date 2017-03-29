<?php 

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application as App;
use Models;
use Symfony\Component\Validator\Constraints as Assert;
use Utils\Utils as Utils;


class Controllers{
	protected $table;

	protected $model;
	protected $app;
	protected $utils;

	protected $constraint;
	protected $access = [];


	public function __construct( App $app, $table ){
		$this->table = $table;

		//Chemin de la class du model;
		$modelPath = '\Models\\'.$table;

		//Instance dynamique du model
		$this->model = new $modelPath($app);
		$this->app = $app;
		$this->utils = new Utils($app);
	}

	/**
	 * Affiche tout les administrateurs de la BDD
	 * @return [view] Retourne la vue associé avec les datas
	 */
	public function index(){
		$this->utils->accessVerif($this->access);

		$data = $this->model->index();	

		return $this->app['twig']->render('fronts/backOffice/'.$this->table.'/index.twig',['data'=>$data]);
	}

	/**
	 * Sert le formulaire de création d'un admin
	 * @return [view] Retourne la vue associé
	 */
	public function create(){
		$this->utils->accessVerif($this->access);

		return $this->app['twig']->render('fronts/backOffice/'.$this->table.'/creer.twig');
	}

	/**
	 * Enregistre l'utilisateur dans la base de donnée.
	 * @param  [Request $request] Donnée issus du formulaire
	 * @return [view]			  Retourne la vue associés² avec les datas
	 */
	public function save(Request $request){
		$this->utils->accessVerif($this->access);

		$request = $request->request->all();

		$this->model->create($request);
		
		return $this->app->redirect('/'.'admin/'.$this->table);
	}

	/**
	 * Supprime un utilisateur de la base
	 * @param  [Request $request Donnée transmise dans la requete
	 * @return [view]           Redirige vers la page de gestiopn des admins avec un message de succes
	 */
	public function delete(Request $request){
		$this->utils->accessVerif($this->access);

		$id = $request->attributes->all()['id'];

		$this->model->delete($id);

		$this->app['session']->getFlashBag()->add('alert', 'Utilisateur supprimé');
		return $this->app->redirect('/'.'admin/'.$this->table);
	}

	/**
	 * Sert le formulaire de mise a jour des admins
	 * @param  [int] $id Id de l'admin a afficher
	 * @return [view]     Retoune la vue avec les datas associées
	 */
	public function edit($id){
		$this->utils->accessVerif($this->access);

		$user = $this->model->read($id);

		return $this->app['twig']->render('fronts/backOffice/'.$this->table.'/edit.twig',['data'=>$user]);
	}

	/**
	 * Enregistré les modifications dans la base de donnée
	 * @param  [Request $request] Donnée issus du formulaire
	 * @return [view]             Redirige vers la page d'administration des admins avec un message
	 */
	public function update(Request $request){
		$this->utils->accessVerif($this->access);

		$request = $request->request->all();
		
		foreach ($request as $key => $value) {
			if ($value == '') {
				unset($request[$key]);
			}
		}

		$this->model->update($request);

		$this->app['session']->getFlashBag()->add('alert', 'Utilisateur modifié');
		return $this->app->redirect('/'.'admin/'.$this->table);
	}

	public function read($id){
		$this->utils->accessVerif($this->access);

		$data = $this->news->read($id);

		return $this->app['twig']->render('fronts/backOffice/'.$this->table.'/read.twig',['data'=>$data]);
	}
}
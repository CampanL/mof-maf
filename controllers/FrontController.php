<?php 

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application as App;
use Models\News as News;
use Symfony\Component\Validator\Constraints as Assert;

class FrontController{
	private $news;
	private $app;

	private $constraint;

	public function __construct( App $app ){

		$this->news = new News($app);
		$this->app = ($app);
	}

	//Sert la page d'accueil des admins
	public function index(){

		$news = $this->news->getNumber(2);

		return $this->app['twig']->render('fronts/home.twig',['news'=>$news]);
	}
}
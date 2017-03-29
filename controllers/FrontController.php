<?php 

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application as App;
use Models\News as News;
use Models\Candidates as Candidates;
use Symfony\Component\Validator\Constraints as Assert;

class FrontController{
	private $news;
	private $app;

	private $constraint;

	public function __construct( App $app ){

		$this->news = new News($app);
		$this->candidates = new Candidates($app);
		$this->app = ($app);
	}

	public function index(){

		$news = $this->news->getNumber(2);

		$nbCandidats = $this->candidates->count();

		$nbCandidats = $nbCandidats[0]->number;

		if ($this->app['session']->get('step'))  $this->app['session']->set('step',null);

		return $this->app['twig']->render('fronts/home.twig',['news'=>$news, 'nbCandidats'=>$nbCandidats]);
	}
}
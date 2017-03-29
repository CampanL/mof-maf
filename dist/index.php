<?php 

	require_once __DIR__.'/../vendor/autoload.php';

	use Silex\Application;

	use Symfony\Component\HttpFoundation\Request;
	Request::enableHttpMethodParameterOverride();

	//Definitions des constantes
	const DB_HOST = 'localhost';
	const DB_DATABASE = 'maf';
	const DB_USER = 'root';
	const DB_PASSWORD = '';
	const URL = 'http://localhost:8080';

	$app = new Application();

	//Ajout des providers
	$app->register(new Silex\Provider\ServiceControllerServiceProvider());
	$app->register(new Silex\Provider\SessionServiceProvider());
	$app->register(new Silex\Provider\ValidatorServiceProvider());

	//Ajout Twig
	$app->register(new Silex\Provider\TwigServiceProvider(), array(
	    'twig.path' => __DIR__.'/../views',
	));

	//Debug On
	$app['debug'] = true;

	//Facade des controlleurs
	$app['front.controller'] = function () use ($app){
		return new \Controllers\FrontController($app);
	};
	$app['backOfficeController.controller'] = function () use ($app){
		return new \Controllers\BackOfficeController($app);
	};
	$app['admin.controller'] = function () use ($app){
		return new \Controllers\AdminController($app);
	};
	$app['news.controller'] = function () use ($app){
		return new \Controllers\NewsController($app);
	};
	$app['candidate.controller'] = function () use ($app){
		return new \Controllers\CandidateController($app);
	};	
	$app['etashbliment.controller'] = function () use ($app){
		return new \Controllers\EtablishmentController($app);
	};
	$app['contracts.controller'] = function () use ($app){
		return new \Controllers\ContractsController($app);
	};

	$app['organizer.controller'] = function () use ($app){
		return new \Controllers\OrganizersController($app);
	};

	//Config PDO
	$app['database.config'] = [
	        'dsn'      => 'mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE,
	        'username' => DB_USER,
	        'password' => DB_PASSWORD,
	        'options'  => [
	                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", // flux en utf8
	                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // mysql erreurs remontées sous forme d'exception
	                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // tous les fetch en objets
	        ]	  
	];

	//Instantiation de PDO
	$app['pdo'] = function( $app ){
		$options = $app['database.config'];

		return new \PDO($options['dsn'],$options['username'],$options['password'],$options['options']);
	};


	/************Root Front Office***************/
	$app->get('/','front.controller:index');

	//Root Candidat
	$app->get('/candidate/create','candidate.controller:create');
	$app->post('/candidate/create','candidate.controller:toSession');
	$app->get('/candidate/save','candidate.controller:save');
	$app->post('/etashbliment/create','etashbliment.controller:toSession');
	$app->post('/contract/create','contracts.controller:toSession');

	/************Root Back Office***************/

	//Root Front
	$app->get('/admin','backOfficeController.controller:adminConnectForm');
	$app->get('/organizer','backOfficeController.controller:organizerConnectForm');
	$app->post('/admin/connect','backOfficeController.controller:adminConnect');
	$app->post('/organizer/connect','backOfficeController.controller:organizerConnect');
	$app->get('/admin/home','backOfficeController.controller:index');
	$app->get('/admin/deconnexion','backOfficeController.controller:deconnexion');
	
	//Root Gestion des admin
	$app->get('/admin/admins','admin.controller:index');
	$app->get('/admin/admins/create','admin.controller:create');
	$app->post('admin/admins/create','admin.controller:save');
	$app->get('/admin/admins/delete/{id}','admin.controller:delete');
	$app->get('/admin/admins/edit/{id}','admin.controller:edit');
	$app->post('/admin/admins/update','admin.controller:update');
	$app->get('/admin/admins/read/{id}','admin.controller:read');

	//Root News
	$app->get('/admin/news','news.controller:index');
	$app->get('/admin/news/create','news.controller:create');
	$app->post('admin/news/create','news.controller:save');
	$app->get('/admin/news/delete/{id}','news.controller:delete');
	$app->get('/admin/news/edit/{id}','news.controller:edit');
	$app->post('/admin/news/update','news.controller:update');
	$app->get('/admin/news/read/{id}','news.controller:read');
	//Root Candidates
	$app->get('/admin/candidates','candidate.controller:index');
	$app->get('/admin/candidates/delete/{id}','candidate.controller:delete');
	$app->get('/admin/candidates/edit/{id}','candidate.controller:edit');
	$app->post('/admin/candidates/update','candidate.controller:update');

	//Root Gestion des admin
	$app->get('/admin/organizers','organizer.controller:index');
	$app->get('/admin/organizers/create','organizer.controller:create');
	$app->post('admin/organizers/create','organizer.controller:save');
	$app->get('/admin/organizers/delete/{id}','organizer.controller:delete');
	$app->get('/admin/organizers/edit/{id}','organizer.controller:edit');
	$app->post('/admin/organizers/update','organizer.controller:update');
	$app->get('/admin/organizers/read/{id}','organizer.controller:read');

	$app->run();
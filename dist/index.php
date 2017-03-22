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
	$app['adminFront.controller'] = function () use ($app){
		return new \Controllers\AdminFrontController($app);
	};
	$app['admin.controller'] = function () use ($app){
		return new \Controllers\AdminController($app);
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


	//Root
	$app->get('/','front.controller:index');

	//Root Back Office
	$app->get('/admin','adminFront.controller:connectForm');
	$app->post('/admin/connect','adminFront.controller:connect');
	$app->get('/admin/home','adminFront.controller:index');
	
	//Root Gestion des admin
	$app->get('/admin/administrateurs','admin.controller:index');
	$app->get('/admin/administrateurs/create','admin.controller:create');
	$app->post('admin/administrateurs/create','admin.controller:save');
	$app->get('/admin/administrateurs/delete/{id}','admin.controller:delete');
	$app->get('/admin/administrateurs/edit/{id}','admin.controller:edit');
	$app->post('/admin/administrateurs/update','admin.controller:update');

	$app->run();
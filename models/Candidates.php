<?php 

namespace Models;

use Silex\Application as App;

class Candidates extends Models
{

	function __construct(App $app)
	{
		$this->app = $app;

		$this->table = 'candidates';
	}

	public function index(){

		$prepare = $this->app['pdo']->prepare('SELECT a.*, c.name as contract_name, e.name as etablishment_name FROM '.$this->table.' as a 
			LEFT JOIN contracts as c 
			ON a.id_contract = c.id
			LEFT JOIN etablishments as e 
			ON a.id_etablishment = e.id'
			);

 
		$prepare->execute();

		$data = $prepare->fetchAll();

		return $data;
	}

}


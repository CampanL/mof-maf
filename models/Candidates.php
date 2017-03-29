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

	public function count(){
		$prepare = $this->app['pdo']->prepare('SELECT COUNT(*) as number from '.$this->table);

 		$prepare->execute();

		$data = $prepare->fetchAll();

		return $data;
	}

}


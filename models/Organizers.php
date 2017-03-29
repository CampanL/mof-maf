<?php 

namespace Models;

use Silex\Application as App;

class Organizers extends Models
{


	function __construct(App $app)
	{
		$this->app = $app;

		$this->table = 'organizers';
	}

	public function connect($data){

		$prepare = $this->app['pdo']->prepare('SELECT a.name,a.second_name, r.name as role FROM organizers as a 
			LEFT JOIN roles as r 
			ON a.id_role = r.id
			WHERE a.mail = (?) AND a.password = (?)'
		);

		$prepare -> bindValue(1,$data['mail'],\PDO::PARAM_STR);
		$prepare -> bindValue(2,$data['password'],\PDO::PARAM_STR);

		$prepare -> execute();

		$result = $prepare -> fetch();

		return $result;
	}
}
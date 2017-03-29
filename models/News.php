<?php 

namespace Models;

use Silex\Application as App;

class News extends Models
{


	function __construct(App $app)
	{
		$this->app = $app;

		$this->table = 'news';
	}

	public function getNumber($nb){
		
		$prepare = $this->app['pdo']->prepare('SELECT * FROM '.$this->table.' ORDER BY id DESC LIMIT ?');

		$prepare -> bindValue(1,$nb,\PDO::PARAM_INT);

		$prepare -> execute();

		$data = $prepare -> fetchAll();

		return $data;
	}
}
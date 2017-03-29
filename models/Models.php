<?php 

namespace Models;

use Silex\Application as App;

class Models
{
	protected $app;

	protected $table;

	function __construct(App $app, $table)
	{
		$this->app = $app;

		$this->table = $table;
	}

	public function index(){

		$prepare = $this->app['pdo']->prepare('SELECT * from '.$this->table.' ORDER BY id DESC');
 
		$prepare->execute();

		$data = $prepare->fetchAll();

		return $data;
	}

	public function create($data){

		$query = 'INSERT INTO '.$this->table.' (';
		
		$champs = ''; //contient le nom des champs de la base
		$val = ''; // associe un '?' a chaque entrée de la BDD

		foreach ($data as $key => $value) {
				$champs .= $key.',';
				$val .= '?,';
		}

		//on supprime la dernier virgule
		$champs[strlen($champs)-1] = ' ';
		$val[strlen($val)-1] = ' ';		

		//Concatène la query
		$query.= $champs.') VALUES ('.$val.')';

		$prepare = $this->app['pdo']->prepare($query);

		//A chaque '?' on associe la valeur issu de la requete
		$index = 1;

		foreach ($data as $key => $value) {

			//On choisit le param a passer a PDO
			if (is_numeric($value)) {
				$param = \PDO::PARAM_INT;
			}else{
				$param = \PDO::PARAM_STR;
			}

			$prepare -> bindValue($index,$value,$param);

			$index++;
		}

		$prepare -> execute();

		return true;
	}

	public function read($id){
	
		$prepare = $this->app['pdo']->prepare('SELECT * FROM '.$this->table.' WHERE id = (?)');

		$prepare -> bindValue(1,$id,\PDO::PARAM_INT);

		$prepare -> execute();

		$data = $prepare -> fetch();

		return $data;
	}

	public function update($data){
		
		$query = 'UPDATE '.$this->table.' SET ';
		
		$set = '';

		// On assosie un "?" a chaque entrée a modifier
		foreach ($data as $key => $value) {
			if ($key == 'id') continue;// L'id ne doit pas avoir de ? associé a ce niveau

			$set .= $key.' = (?), ';
		}

		//On supprime la derniere virgule
		$set[strlen($set)-2] = ' ';

		$query .= $set. ' WHERE id = (?)';

		$prepare = $this->app['pdo']->prepare($query);

		$index = 1;

		foreach ($data as $key => $value) {
			if ($key == 'id') continue; // L'id n'a pas de ? associé a ce niveau

			if (is_numeric($value)) {
				$param = \PDO::PARAM_INT;
			}else{
				$param = \PDO::PARAM_STR;
			}

			$prepare -> bindValue($index,$value,$param);
			$index++;

		}

		$prepare -> bindValue($index++,$data['id'],\PDO::PARAM_INT); // On bind la valeur de l'id au niveau de "WHERE id= (?)"
		
		$prepare -> execute();

		return true;
	}

	public function delete($id){
	
		$prepare = $this->app['pdo']->prepare('DELETE FROM '.$this->table.' WHERE id = (?)');

		$prepare -> bindValue(1,$id,\PDO::PARAM_INT);

		$prepare -> execute();

		return true;
	}

	public function selectWhere($where){
		
		$prepare = $this->app['pdo']->prepare('SELECT * FROM '.$this->table.' WHERE '.$where);

		$prepare -> execute();

		$data = $prepare -> fetchAll();

		return $data;
	}
}
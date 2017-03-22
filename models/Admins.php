<?php 

namespace Models;

use Silex\Application as App;

class Admins
{

	private $app;
	
	function __construct(App $app)
	{
		$this->app = $app;
	}

	public function index(){
		$prepare = $this->app['pdo']->prepare('SELECT * from admins');

		$prepare->execute();

		$data = $prepare->fetchAll();

		return $data;
	}

	public function create($data){

		$prepare = $this->app['pdo']->prepare("SELECT * FROM admins WHERE mail = ?");

		$prepare -> bindValue(1,$data['mail'],\PDO::PARAM_STR);
		
		$prepare -> execute();

		$results = $prepare->fetchAll();


		if (!empty($results)) {
			return false;
		}

		$prepare = $this->app['pdo']->prepare('INSERT INTO admins (mail,password) VALUES (?,?)');

		$prepare -> bindValue(1,$data['mail'],\PDO::PARAM_STR);
		$prepare -> bindValue(2,sha1($data['password']),\PDO::PARAM_STR);

		$prepare -> execute();

		return true;
	}

	public function read($id){
	
		$prepare = $this->app['pdo']->prepare('SELECT * FROM admins WHERE id = (?)');

		$prepare -> bindValue(1,$id,\PDO::PARAM_INT);

		$prepare -> execute();

		$data = $prepare -> fetch();

		return $data;
	}

	public function update($data){
	
		$prepare = $this->app['pdo']->prepare('UPDATE admins SET 
			mail = (?),
			password = (?)
			WHERE id = (?)');

		$prepare -> bindValue(1,$data['mail'],\PDO::PARAM_STR);
		$prepare -> bindValue(2,sha1($data['password']),\PDO::PARAM_STR);
		$prepare -> bindValue(3,$data['id'],\PDO::PARAM_INT);

		$prepare -> execute();

		return true;
	}

	public function delete($id){
	
		$prepare = $this->app['pdo']->prepare('DELETE FROM admins WHERE id = (?)');

		$prepare -> bindValue(1,$id,\PDO::PARAM_INT);

		$prepare -> execute();

		return true;
	}

	public function connect($data){

		$prepare = $this->app['pdo']->prepare('SELECT * FROM admins WHERE mail = (?) AND password = (?)');

		$prepare -> bindValue(1,$data['mail'],\PDO::PARAM_STR);
		$prepare -> bindValue(2,sha1($data['password']),\PDO::PARAM_STR);

		$prepare -> execute();

		$result = $prepare -> fetch();

		return $result;
	}
}
<?php 

namespace Models;

use Silex\Application as App;

class News
{

	private $app;
	
	function __construct(App $app)
	{
		$this->app = $app;
	}

	public function index(){
		$prepare = $this->app['pdo']->prepare('SELECT * from news DESC');

		$prepare->execute();

		$data = $prepare->fetchAll();

		return $data;
	}

	public function create($data){

		$prepare = $this->app['pdo']->prepare('INSERT INTO news (title,content,exerpt,published_at) VALUES (?,?,?,?)');

		$prepare -> bindValue(1,$data['title'],\PDO::PARAM_STR);
		$prepare -> bindValue(2,$data['content'],\PDO::PARAM_STR);
		$prepare -> bindValue(3,$data['exerpt'],\PDO::PARAM_STR);
		$prepare -> bindValue(4,$data['published_at'],\PDO::PARAM_STR);

		$prepare -> execute();

		return true;
	}

	public function read($id){
	
		$prepare = $this->app['pdo']->prepare('SELECT * FROM news WHERE id = (?)');

		$prepare -> bindValue(1,$id,\PDO::PARAM_INT);

		$prepare -> execute();

		$data = $prepare -> fetch();

		return $data;
	}

	public function update($id,$data){
	
		$prepare = $this->app['pdo']->prepare('UPDATE news SET 
			title = (?),
			exerpt = (?),
			content = (?)
			WHERE id = (?)');

		$prepare -> bindValue(1,$data['title'],\PDO::PARAM_STR);
		$prepare -> bindValue(2,$data['exerpt'],\PDO::PARAM_STR);
		$prepare -> bindValue(3,$data['published_at'],\PDO::PARAM_STR);
		$prepare -> bindValue(4,$id,\PDO::PARAM_INT);

		$prepare -> execute();

		return true;
	}

	public function delete($id){
	
		$prepare = $this->app['pdo']->prepare('DELETE FROM news WHERE id = (?)');

		$prepare -> bindValue(1,$id,\PDO::PARAM_INT);

		$prepare -> execute();

		return true;
	}

	public function getNumber($nb){

		$prepare = $this->app['pdo']->prepare('SELECT * from news ORDER BY published_at DESC LIMIT ?;');

		$prepare -> bindValue(1,$nb,\PDO::PARAM_INT);

		$prepare->execute();

		$data = $prepare->fetchAll();

		return $data;
	}
}
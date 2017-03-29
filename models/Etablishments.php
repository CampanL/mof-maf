<?php 

namespace Models;

use Silex\Application as App;

class Etablishments extends Models
{

	function __construct(App $app)
	{
		$this->app = $app;

		$this->table = 'etablishments';
	}
}
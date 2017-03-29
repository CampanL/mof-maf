<?php 

namespace Models;

use Silex\Application as App;

class Contracts extends Models
{

	function __construct(App $app)
	{
		$this->app = $app;

		$this->table = 'contracts';
	}
}
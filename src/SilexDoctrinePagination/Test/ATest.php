<?php
/**
 * @author Dolgov_M <mdol@1c>
 * @date 04.12.13 at 17:57
 */

namespace SilexDoctrinePagination\Test;


use Doctrine\DBAL\Connection;
use Silex\Application;
use Silex\WebTestCase;

abstract class ATest extends WebTestCase{
	/**
	 * @var Application
	 */
	protected  $app;

	public function createApplication(){
		global $app;
		$app['debug'] = true;
		$app['session.test'] = true;
		$app['orm.default_cache'] = "array";
		$this->app = $app;
		$this->app->boot();
		return $app;
	}

	/**
	 * @return Connection
	 */
	protected function getDoctrineConnection(){
		return $this->app['db'];
	}

} 
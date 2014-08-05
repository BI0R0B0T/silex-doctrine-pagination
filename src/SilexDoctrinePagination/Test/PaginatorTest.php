<?php
/**
 * @author Dolgov_M <mdol@1c.ru> at 14.05.14 13:14
 */

namespace SilexDoctrinePagination\Test;


use SilexDoctrinePagination\Adapter\DoctrineEntityAdapter;
use SilexDoctrinePagination\PaginationServiceProvider;
use SilexDoctrinePagination\Paginator;
use SilexDoctrinePagination\Test\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class PaginatorTest extends ATest{
	/**
	 * @var Paginator
	 */
	private $paginator;

	public function testProvider(){
		$this->app["request"] = Request::create("/");
		$paginator = $this->getNewPaginator();
		$this->assertTrue($paginator instanceof Paginator);

		$secondPaginator = $this->getNewPaginator();
		$this->assertTrue($paginator == $secondPaginator);
		$this->assertFalse($paginator === $secondPaginator);
	}

	public function testProviderWithDoctrineEntityAdapter(){
		$paginator = $this->getPaginator();
		$adapter = new DoctrineEntityAdapter($this->getDoctrineConnection(),User::getClassName());

		$paginator->setAdapter($adapter);
		$maxPerPage = $paginator->getMaxPerPage();
		$entityCount = $adapter->count();
		$expectedCount = $maxPerPage > $entityCount?$entityCount:$maxPerPage;
		$realCount = 0;
		foreach($paginator->getResult() as $entity){
			$realCount++;
			$this->assertTrue($entity instanceof User);
		}
		$this->assertEquals($expectedCount,$realCount);
	}

	/**
	 * @return Paginator
	 */
	protected function getPaginator() {
		if(is_null($this->paginator)){
			return $this->getNewPaginator();
		}else{
			return $this->paginator;
		}
	}

	protected function getNewPaginator(){
		if (!$this->app->offsetExists("paginator")) {
			$provider = new PaginationServiceProvider();
			$provider->register($this->app);
			$provider->boot($this->app);
		}
		$this->paginator = $this->app["paginator"];
		return $this->paginator;
	}
}
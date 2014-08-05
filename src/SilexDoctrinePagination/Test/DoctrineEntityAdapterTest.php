<?php
/**
 * @author Dolgov_M <mdol@1c.ru> at 16.05.14 16:09
 */

namespace Its\Paginator\Test;


use SilexDoctrinePagination\Adapter\DoctrineEntityAdapter;
use SilexDoctrinePagination\Test\ATest;
use SilexDoctrinePagination\Test\Entity\User;

class DoctrineEntityAdapterTest extends ATest{

	private $adapter;

    /**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidClass(){
		new DoctrineEntityAdapter($this->getDoctrineConnection(),"INVALID_CLASS_NAME");
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testExceptionSetSortableNotArray(){
		$this->getAdapter()->setSortable("not array");
	}
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testExceptionSetSortableEmptyArray(){
		$this->getAdapter()->setSortable(array());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testExceptionSetOrderBy(){
		$this->getAdapter()->setOrderBy("not_exist_field");
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testExceptionSetFilter(){
		$this->getAdapter()->setFilter("not_exist_field","some_value");
	}

	public function testSetSortable(){
		$defaultArray = array(DoctrineEntityAdapter::DEFAULT_FIELD);
		$this->assertEquals($defaultArray, $this->getAdapter()->getSortableCode() );

		$fieldList = array("firstKey",);
		$equalFieldList = array("firstKey" => "firstKey");
		$expected = array("firstKey",);

		$this->assertTrue($this->getAdapter()->setSortable($fieldList) instanceof DoctrineEntityAdapter);
		$this->assertEquals($expected, $this->getAdapter()->getSortableCode());
		$this->getAdapter()->setSortable($equalFieldList);
		$this->assertEquals($expected, $this->getAdapter()->getSortableCode());


		$fieldList = array("firstKey","secondKey" => "thirdKey", "5");
		$expected = array("firstKey","secondKey","5");
		$this->getAdapter()->setSortable($fieldList);
		$this->assertEquals($expected, $this->getAdapter()->getSortableCode());

		$this->getAdapter()->setSortable($defaultArray);
	}

	public function testSetOrderBy(){
		$expected = array(DoctrineEntityAdapter::DEFAULT_FIELD,DoctrineEntityAdapter::ASC);
		$this->getAdapter()->setOrderBy(DoctrineEntityAdapter::DEFAULT_FIELD);
		$this->assertEquals($expected,$this->getAdapter()->getOrderBy());
		$this->getAdapter()->setOrderBy(DoctrineEntityAdapter::DEFAULT_FIELD, "NOT VALID DIRECTION");
		$this->assertEquals($expected,$this->getAdapter()->getOrderBy());
		$this->getAdapter()->setOrderBy(DoctrineEntityAdapter::DEFAULT_FIELD,DoctrineEntityAdapter::DESC);
		$expected = array(DoctrineEntityAdapter::DEFAULT_FIELD,DoctrineEntityAdapter::DESC);
		$this->assertEquals($expected,$this->getAdapter()->getOrderBy());
	}

	public function testCount(){
		$count = $this->getAdapter()->count();
		$this->assertTrue(is_int($count));
		printf("User count = %d".PHP_EOL, $count);
	}

	public function testGetSliceIterator(){
		$userCount = $this->getAdapter()->count();
		$count = $userCount >= 5 ? 5 : $userCount;
		$iterator =  $this->getAdapter()->getSliceIterator(1,$count);
		$this->assertTrue($iterator instanceof \ArrayIterator);
		$i = 0;
		foreach($iterator as $v){
			$this->assertTrue($v instanceof User);
			$i++;
		}
		$this->assertEquals($count,$i);
	}

	public function testFilter(){
		$this->getAdapter()->setFilter(DoctrineEntityAdapter::DEFAULT_FIELD,"ab");
		$this->testGetSliceIterator();
	}

	/**
	 * @return DoctrineEntityAdapter
	 */
	protected  function getAdapter() {
		if(is_null($this->adapter)){
			$this->adapter = new DoctrineEntityAdapter($this->getDoctrineConnection(),User::getClassName());
		}
		return $this->adapter;
	}

} 
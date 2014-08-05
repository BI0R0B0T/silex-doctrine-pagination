<?php
/**
 * @author Dolgov_M <mdol@1c.ru> at 16.05.14 12:39
 */

namespace SilexDoctrinePagination\Adapter;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use SilexDoctrinePagination\Iterator\ArrayIterator;

class DoctrineEntityAdapter implements  IAdapter{

	const DEFAULT_FIELD = "id";

	/**
	 * @var Connection
	 */
	protected $connection;
	/**
	 * @var string
	 */
	protected $className;

	protected $filter;

	protected $sortable = array(self::DEFAULT_FIELD => self::DEFAULT_FIELD);

	protected $orderBy = array(self::DEFAULT_FIELD,self::ASC);

	private $count;

	protected $filterable = array(self::DEFAULT_FIELD);

	public function __construct(Connection $connection, $entityClassName){
		$this->connection = $connection;
		if(!class_exists($entityClassName)){
			throw new \InvalidArgumentException("Class name $entityClassName not exist");
		}
		$this->className = $entityClassName;
	}

	/**
	 * @inheritdoc
	 */
	public function getSliceIterator($offset, $length) {
        $qb = $this->getQueryBuilder();
        $qb->setMaxResults($length);
        $qb->setFirstResult($offset);
        return new ArrayIterator( $qb->execute()->fetchAll(), $offset );
	}

	/**
	 * @inheritdoc
	 */
	public function setSortable($fieldArray) {
		if(!is_array($fieldArray)){
			throw new \InvalidArgumentException("fieldArray must be array");
		}
		if(empty($fieldArray)){
			throw new \InvalidArgumentException("fieldArray not to be empty");
		}
		$this->sortable = array();
		foreach($fieldArray as $k=>$v){
			if(!is_string($v)){ throw new \InvalidArgumentException("fieldName must be a string"); }
			if(is_numeric($k)){
				$this->sortable[$v] = $v;
			}else{
				$this->sortable[$k] = $v;
			}
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSortableCode() {
		return array_keys($this->sortable);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderBy($fieldCode, $direction = self::ASC) {
		$this->checkFieldCode($fieldCode);
		if($direction != self::ASC && $direction != self::DESC){
			$direction = self::ASC;
		}
		$this->orderBy = array($fieldCode,$direction);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getOrderBy() {
		return $this->orderBy;
	}

	/**
	 * @inheritdoc
	 */
	public function setFilter($fieldCode, $value) {
		$this->checkFieldCode($fieldCode);
		$this->filter = array($fieldCode,$value);
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setFilterable($fieldCodeArray) {
		foreach($fieldCodeArray as $v){
			$this->checkFieldCode($v);
		}
		$this->filterable = $fieldCodeArray;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getFilterable() {
		$ret = array();
		foreach($this->filterable as $v){
			$this->checkFieldCode($v);
			$ret[$v] = $this->sortable[$v];
		}
		return $ret;
	}


	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 */
	public function count() {
		if(is_null($this->count)){
            $qb = $this->getQueryBuilder();
            $qb->select("count(e)");
            $qb->setMaxResults(1);
            $this->count = (int)$qb->execute()->fetch();
		}
		return $this->count;
	}

	/**
	 * @return QueryBuilder
	 */
	protected function getQueryBuilder() {
		list($f, $d) = $this->orderBy;
		$qb = $this->connection
			->createQueryBuilder()
			->select("e")
			->from($this->className, "e")
			->orderBy("e.".$this->sortable[$f],$d);
		if (!is_null($this->filter)) {
			list($code, $value) = $this->filter;
			$qb->where(sprintf("e.%s LIKE :value", $this->sortable[$code]))
				->setParameter("value", $value."%");
		}
		return $qb;
	}

	/**
	 * @param $fieldCode
	 * @throws \InvalidArgumentException
	 */
	protected function checkFieldCode($fieldCode) {
		if (!isset($this->sortable[$fieldCode])) {
			throw new \InvalidArgumentException("fieldCode \"$fieldCode\" not exist");
		}
	}


} 
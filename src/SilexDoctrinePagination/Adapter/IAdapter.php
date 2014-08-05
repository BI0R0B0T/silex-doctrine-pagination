<?php
/**
 * @author Dolgov_M <mdol@1c.ru> at 14.05.14 12:55
 */

namespace SilexDoctrinePagination\Adapter;


interface IAdapter extends \Countable{

	const ASC = "ASC";
	const DESC = "DESC";

	/**
	 * @param int $offset
	 * @param int $length
	 * @return \Iterator
	 */
	public  function getSliceIterator($offset,$length);

	/**
	 * @param array $fieldArray "fieldCode" => string|array
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setSortable($fieldArray);

	/**
	 * @return array
	 */
	public function getSortableCode();

	/**
	 * @param string $fieldCode
	 * @param string $direction
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setOrderBy($fieldCode, $direction = self::ASC);

	/**
	 * @return array (fieldCode, direction)
	 */
	public function getOrderBy();

	/**
	 * @param string $fieldCode
	 * @param string $value
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setFilter($fieldCode, $value);

	/**
	 * @param array $fieldCodeArray ("fieldCode" from setSortable)
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setFilterable($fieldCodeArray);

	/**
	 * @return array ("fieldCode" => field name)
	 */
	public function getFilterable();
} 
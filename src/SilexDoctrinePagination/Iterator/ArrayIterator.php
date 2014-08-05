<?php
/**
 * @author Dolgov_M <mdol@1c.ru> at 22.05.14 14:13
 */

namespace SilexDoctrinePagination\Iterator;


class ArrayIterator extends \ArrayIterator{

	protected $firstElement = 1;

	public function __construct($array = array(), $offset, $flags=0) {
		parent::__construct($array, $flags);
		$this->firstElement = $offset+1;
	}


	public function key() {
		return parent::key() + $this->firstElement;
	}


} 
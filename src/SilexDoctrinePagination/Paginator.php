<?php
/**
 * @authot Dolgov_M <mdol@1c.ru>
 * @date 17.07.14 12:44
 */

namespace SilexDoctrinePagination;


use SilexDoctrinePagination\Adapter\IAdapter;
use SilexDoctrinePagination\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class Paginator {
    const DEFAULT_PREFIX = "p_";
    const PAGE = "page";
    const ORDER_BY = "ord";
    const DIRECTION = "dir";
    const DEFAULT_PAGE_NUMBER = 1;
    const DEFAULT_ON_PAGE = 10;
    const MAX_COUNT = "max";
    const PAGE_FIRST = "<<";
    const PAGE_PREV = "<";
    const COUNT_PAGE_MARGIN = 2;
    const PAGE_NEXT = ">";
    const PAGE_LAST = ">>";
    const FILTER_CODE  = "f_c";
    const FILTER_VALUE = "f_v";

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var IAdapter
     */
    private $adapter;

    private $currentPage;

    private $maxPerPage;

    private $currentUrl;

    private $prefix = self::DEFAULT_PREFIX;

    private $orderBy;

    private $direction;

    private $defaultOrderBy;

    private $defaultDirection;

    private $filter;

    public function __construct(\Twig_Environment $twig, Request $request){
        $this->twig = $twig;
        $this->request = $request;
        $this->currentUrl = $request->getPathInfo();
    }

    /**
     * @param IAdapter $adapter
     * @return Paginator
     */
    public function setAdapter(IAdapter $adapter) {
        $this->adapter = $adapter;
        $this->getDefaultDirection();
        return $this;
    }

    /**
     * @return IAdapter
     * @throws Exception
     */
    public function getAdapter() {
        if(is_null($this->adapter)){
            throw new Exception("Adapter not send!");
        }
        return $this->adapter;
    }

    public function getResult(){
        return $this->getCurrentPageResultFromAdapter();
    }

    /**
     * @return int
     */
    public function getCurrentPage() {
        if(is_null($this->currentPage)){
            $this->currentPage = $this->getFromRequest(self::PAGE);
            if(!$this->currentPage){ $this->currentPage = self::DEFAULT_PAGE_NUMBER; }
        }
        return $this->currentPage;
    }

    /**
     * @param int $maxPerPage
     * @return $this
     * @throws
     */
    public function setMaxPerPage($maxPerPage) {
        $int = (int)$maxPerPage;
        if(!$int>0){
            throw new \InvalidArgumentException("Max Per Page must be positive");
        }
        $this->maxPerPage = $int;
        return $this;
    }

    /**
     * @param mixed $filter
     * @return Paginator
     */
    public function setFilter($filter) {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @return array
     */
    public function getFilter() {
        if(is_null($this->filter)){
            $code = $this->getFromRequest(self::FILTER_CODE);
            $value = $this->getFromRequest(self::FILTER_VALUE);
            if($code && $value){
                $this->adapter->setFilter($code,$value);
                $this->filter = array($code,$value);
            }else{
                $this->filter = array();
            }
        }
        return $this->filter;
    }



    /**
     * @return int
     */
    public function getMaxPerPage() {
        if(is_null($this->maxPerPage)){
            $this->maxPerPage = (int)$this->getFromRequest(self::MAX_COUNT);
            if(!$this->maxPerPage){ $this->maxPerPage = self::DEFAULT_ON_PAGE; }
        }
        return $this->maxPerPage;
    }

    public function hasPreviousPage(){
        return 1 < $this->getCurrentPage();
    }

    public function hasNextPage(){
        return $this->getPageCount() > $this->getCurrentPage();
    }

    public function hasPage($page){
        return $page >0 && $page <= $this->getPageCount();
    }

    public function getPageCount(){
        static $pageCount;
        if(is_null($pageCount)){
            $count = $this->getAdapter()->count();
            if( $count%$this->getMaxPerPage() ) {
                $pageCount = (int)( $count/$this->getMaxPerPage()+1 );
            }else{
                $pageCount = (int)( $count/$this->getMaxPerPage() );
            }
            return $pageCount ? $pageCount : 1;
        }
        return $pageCount ? $pageCount : 1;
    }

    public function getPageLink($pageNumber, $optionInString){
        return sprintf("%s?%s=%d%s",
            $this->currentUrl,
            $this->getParameterName(self::PAGE),      $pageNumber,
            $optionInString
        );
    }

    public function getOrderLink($field){
        $options = array();
        if($field != $this->getDefaultOrderBy()){
            $options[] = $this->getParameterName(self::ORDER_BY)."=".$field;
        }
        $direction = $this->getOrderDirection($field);
        if($this->getDefaultDirection() != $direction){
            $options[] = $this->getParameterName(self::DIRECTION)."=".$direction;
        }
        if($this->getMaxPerPage() != self::DEFAULT_ON_PAGE){
            $options[] =$this->getParameterName(self::MAX_COUNT)."=".$this->getMaxPerPage();
        }
        if($this->getFilter()){
            list($code,$value) = $this->getFilter();
            $options[] = $this->getParameterName(self::FILTER_CODE) ."=".$code;
            $options[] = $this->getParameterName(self::FILTER_VALUE)."=".$value;
        }
        if(empty($options)){
            return $this->currentUrl;
        }else{
            return $this->currentUrl."?".implode("&",$options);
        }
    }

    public function getOrderDirection($field){
        if($this->getOrderBy() == $field){
            $direction = $this->getDirection() == IAdapter::ASC ? IAdapter::DESC : IAdapter::ASC;
        }else{
            $direction = IAdapter::ASC;
        }
        return $direction;
    }
    /**
     * @param string $prefix
     * @return Paginator
     */
    public function setPrefix($prefix) {
        $this->prefix = (string)$prefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy() {
        if(is_null($this->orderBy)){
            $this->orderBy = $this->getFromRequest(self::ORDER_BY);
            if($this->orderBy){
                $this->adapter->setOrderBy($this->orderBy,$this->getDirection());
            }else{
                $this->orderBy = $this->getDefaultOrderBy();
            }
        }
        return $this->orderBy;
    }

    /**
     * @return string
     */
    public function getDirection() {
        if(is_null($this->direction)){
            $this->direction = $this->getFromRequest(self::DIRECTION);
            if($this->direction){
                $this->adapter->setOrderBy($this->getOrderBy(),$this->direction);
            }else{
                $this->direction = $this->getDefaultDirection();
            }
        }
        return $this->direction;
    }

    public function getLinks(){
        $options = array();
        if($this->getOrderBy() != $this->getDefaultOrderBy()){
            $options[] = $this->getParameterName(self::ORDER_BY)."=".$this->getOrderBy();
        }
        if($this->getDirection() != $this->getDefaultDirection()){
            $options[] = $this->getParameterName(self::DIRECTION)."=".$this->getDirection();
        }
        if($this->getMaxPerPage() != self::DEFAULT_ON_PAGE){
            $options[] =$this->getParameterName(self::MAX_COUNT)."=".$this->getMaxPerPage();
        }
        if($this->getFilter()){
            list($code,$value) = $this->getFilter();
            $options[] = $this->getParameterName(self::FILTER_CODE) ."=".$code;
            $options[] = $this->getParameterName(self::FILTER_VALUE)."=".$value;
        }
        $optionsToString = empty($options) ? "" : "&".implode("&",$options);

        $linkArray = array();

        if($this->getCurrentPage() > self::COUNT_PAGE_MARGIN+1){
            $linkArray[self::PAGE_FIRST] = $this->getPageLink(1,$optionsToString);
        }
        if($this->hasPreviousPage()){
            $linkArray[self::PAGE_PREV] = $this->getPageLink($this->getCurrentPage()-1,$optionsToString);
        }
        $leftBorder = $this->getCurrentPage() - self::COUNT_PAGE_MARGIN;
        $rightBorder = $this->getCurrentPage() + self::COUNT_PAGE_MARGIN;
        for($i = $leftBorder; $i <= $rightBorder; $i++){
            if($this->hasPage($i)){
                $linkArray[$i] = $this->getPageLink($i,$optionsToString)
                ;			}
        }
        if($this->hasNextPage()){
            $linkArray[self::PAGE_NEXT] = $this->getPageLink($this->getCurrentPage()+1,$optionsToString);
        }
        if($this->getCurrentPage() < $this->getPageCount()-self::COUNT_PAGE_MARGIN-1){
            $linkArray[self::PAGE_LAST] = $this->getPageLink($this->getPageCount(),$optionsToString);
        }
        return $linkArray;
    }

    protected function getCurrentPageResultFromAdapter(){
        $offset = $this->calculateOffsetForCurrentPageResults();
        return $this->getAdapter()->getSliceIterator($offset,$this->getMaxPerPage());
    }

    protected function calculateOffsetForCurrentPageResults(){
        return ($this->getCurrentPage() - 1) * $this->getMaxPerPage();
    }

    public function getParameterName($code){
        return $this->prefix.$code;
    }

    /**
     * @return string
     */
    protected function getDefaultDirection() {
        if(is_null($this->defaultDirection)){
            list($this->defaultOrderBy, $this->defaultDirection) = $this->getAdapter()->getOrderBy();
        }
        return $this->defaultDirection;
    }

    /**
     * @return string
     */
    protected function getDefaultOrderBy() {
        if(is_null($this->defaultOrderBy)){
            list($this->defaultOrderBy, $this->defaultDirection) = $this->getAdapter()->getOrderBy();
        }
        return $this->defaultOrderBy;
    }

    /**
     * @param string $code
     * @return mixed
     */
    protected function getFromRequest($code){
        return $this->request->query->get($this->getParameterName($code));
    }

} 
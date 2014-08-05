<?php
/**
 * @author Dolgov_M <mdol@1c.ru> at 21.05.14 16:31
 */

namespace Its\Paginator\Twig;


use Silex\Application;
use SilexDoctrinePagination\PaginationServiceProvider;
use SilexDoctrinePagination\Paginator;

class Extension {

	/**
	 * @param Application $app
	 * @return array
	 */
	public static function getCallbackArray(Application $app){
		return array(
			"render_paginator" =>  function(Paginator $paginator) use ($app){
					/**
					 * @var $twig \Twig_Environment
					 */
					$twig = $app["twig"];
					$option = $app["paginator.option"];
					if($paginator->getPageCount() > 1){
						$data = array(
							"paginator" => $paginator,
						);
						return $twig->render($option[PaginationServiceProvider::PAGINATOR_TEMPLATE],$data);
					}else{
						return "";
					}
				},
			"render_p_link" => function($fieldCode, Paginator $paginator) use ($app) {
					/**
					 * @var $twig \Twig_Environment
					 */
					$twig = $app["twig"];
					$option = $app["paginator.option"];
					$data = array(
						"href" => $paginator->getOrderLink($fieldCode),
						"order" => $paginator->getOrderDirection($fieldCode),
					);
					return $twig->render($option[PaginationServiceProvider::PAGINATOR_ARROW],$data);
				},
			"render_p_filter" => function(Paginator $paginator) use ($app) {
					/**
					 * @var $twig \Twig_Environment
					 */
					$twig = $app["twig"];
					$option = $app["paginator.option"];
					$data = array(
						"filterCode" => $paginator->getParameterName(Paginator::FILTER_CODE),
						"filterValue"=> $paginator->getParameterName(Paginator::FILTER_VALUE),
						"filterable" => $paginator->getAdapter()->getFilterable(),
						"activeFilterCode" => "",
						"activeFilterValue"=> "",
					);
					if($paginator->getFilter()){
						list($data["activeFilterCode"], $data["activeFilterValue"]) = $paginator->getFilter();
					}
					return $twig->render($option[PaginationServiceProvider::PAGINATOR_FILTER],$data);
				}
		);
	}
} 
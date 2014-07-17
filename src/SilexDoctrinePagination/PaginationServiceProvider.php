<?php
/**
 * @authot Dolgov_M <mdol@1c.ru>
 * @date 17.07.14 12:31
 */

namespace SilexDoctrinePagination;


use Silex\Application;
use Silex\ServiceProviderInterface;

class PaginationServiceProvider implements ServiceProviderInterface{

    const PAGINATOR_TEMPLATE = "paginator_template";
    const PAGINATOR_ARROW    = "paginator_arrow";
    const PAGINATOR_FILTER   = "paginator_filter_template";

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app) {
        $app["paginator"] = function($app) {
            $app["paginator.option"] = array_replace(array(
                PaginationServiceProvider::PAGINATOR_TEMPLATE => "default_paginator.html.twig",
                PaginationServiceProvider::PAGINATOR_ARROW    => "default_paginator_arrow.html.twig",
                PaginationServiceProvider::PAGINATOR_FILTER   => "default_paginator_filter.html.twig",
            ),$app["paginator.option"]);
            return new Paginator($app["twig"], $app["request"]);
        };
        if($app->offsetExists("twig")) {
//            $app['twig'] = $app->share($app->extend('twig', function(\Twig_Environment $twig) use($app){
//                foreach( Extension::getCallbackArray($app) as $name => $callable){
//                    $twig->addFunction(new \Twig_SimpleFunction($name,$callable,array("is_safe" => array("html"))));
//                }
//                return $twig;
//            }));
        }
        $app["paginator.option"] = array();
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app) {

    }

} 
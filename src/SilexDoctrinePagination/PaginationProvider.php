<?php
/**
 * @authot Dolgov_M <mdol@1c.ru>
 * @date 17.07.14 12:31
 */

namespace SilexDoctrinePagination;


use Silex\Application;
use Silex\ServiceProviderInterface;

class PaginationProvider implements ServiceProviderInterface{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app) {
        // TODO: Implement register() method.
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app) {
        // TODO: Implement boot() method.
    }

} 
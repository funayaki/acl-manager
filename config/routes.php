<?php

use Cake\Core\Configure;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;

Router::prefix('admin', function ($routes) {
    $routes->plugin('AclManager', function ($routes) {
        $routes->fallbacks(DashedRoute::class);
    });
});

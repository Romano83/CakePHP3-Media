<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin ( 'Media', function ( RouteBuilder $routes) {
	$routes->fallbacks ( 'InflectedRoute' );
} );

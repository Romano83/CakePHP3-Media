<?php
use Cake\Routing\Router;

Router::plugin ( 'Media', function ($routes) {
	$routes->fallbacks ( 'InflectedRoute' );
} );

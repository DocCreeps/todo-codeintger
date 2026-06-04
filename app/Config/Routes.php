<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'TodoController::index');

$routes->post('/store', 'TodoController::store');

$routes->get('/complete/(:num)', 'TodoController::complete/$1');

$routes->get('/delete/(:num)', 'TodoController::delete/$1');
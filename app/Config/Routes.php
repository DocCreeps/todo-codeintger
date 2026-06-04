<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'TodoController::index');
$routes->post('/store', 'TodoController::store');
$routes->post('/todo/toggle/(:num)', 'TodoController::toggle/$1');
$routes->delete('/todo/delete/(:num)', 'TodoController::delete/$1');
$routes->post('/todo/reorder', 'TodoController::reorder');
$routes->get('/clear', 'TodoController::clearCompleted');
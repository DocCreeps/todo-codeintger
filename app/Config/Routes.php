<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'TodoController::index');

$routes->post('/store', 'TodoController::store');

/**
 * ACTIONS KANBAN
 */
$routes->post('/todo/toggle/(:num)', 'TodoController::toggle/$1'); // optionnel (UX rapide)

$routes->post('/todo/move/(:num)', 'TodoController::move/$1'); // drag entre colonnes

/**
 * SOFT DELETE (ARCHIVE SYSTEM)
 */
$routes->post('/todo/archive/(:num)', 'TodoController::archive/$1');
$routes->post('/todo/restore/(:num)', 'TodoController::restore/$1');

/**
 * REORDER (drag dans une colonne)
 */
$routes->post('/todo/reorder', 'TodoController::reorder');

/**
 * OPTIONNEL (si tu veux garder un filtre UI)
 */
$routes->get('/clear', 'TodoController::clearCompleted');

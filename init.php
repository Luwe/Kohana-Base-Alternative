<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Set the default routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 * 
 * Default routes provided are:
 * - widget route (for sub and ajax requests)
 * - page wide route
 */     
Route::set('widget', '<directory>/<controller>/<action>(/<id>)(<format>)(/<overflow>)', 
  array(
    'format' => '\.\w+',
    'overflow' => '.*'
  ))
  ->defaults(array(
    'format'     => '.html',
    'controller' => 'error',
    'action'     => 'index',
  ));
  
Route::set('page', '(<controller>(/<action>(/<id>(/<overflow>))))', array('overflow' => '.*'))
  ->defaults(array(
    'format'     => '.html',
    'controller' => 'home',
    'action'     => 'index',
  ));

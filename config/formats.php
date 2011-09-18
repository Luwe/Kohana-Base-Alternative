<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Available response formats. Response formats are setup as follows:
 *
 * Define a format as follows:
 *
 *     '.format' => array(
 *       string  layout       The full global template for this format,
 *       string  subtemplate  The subtemplate for this format,
 *       string  type         The accept-header type indication,
 *     )
 *
 */
return array(
  '.html' => array(
    'layout' => 'default',
    'subtemplate' => NULL,
    'type' => 'text/html',
  ),
  '.json' => array(
    'layout' => 'json', 
    'subtemplate' => 'json',
    'type' => 'application/json',
  ),
);

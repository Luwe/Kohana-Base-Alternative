<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Placeholder for global methods and attributes used in all View objects
 * 
 * Can only be extended
 * 
 * To ensure interchangeability of a view object with different formats (XML, JSON, HTML) raw HTML should
 * not be planted in View objects. HTML and Form helper functions should therefore not be used
 * when different formats are desired.
 * 
 * @package    LJCore
 * @author     Lieuwe Jan Eilander
 * @copyright  (c) 2010-2011 Lieuwe Jan Eilander
 */
abstract class Ljcore_View_Core extends Kostache_Layout {

  /**
   * Config file to get initial layout settings from
   * @var  string
   */
  protected $_layout_config = 'website';
  
  /**
   * Add initial settings from a config file to an existing array
   * 
   * @param   mixed  array to prepend initial settings to
   * @param   mixed  config file
   * @param   mixed  config variable (dotnotation enabled type.subtype etc.)
   * @return  array
   */
  protected function _add_initial_settings(array $var, $type = 'css')
  {
    $type = ($type) ? '.'. (string) $type : '';
    
    // Check if initial array exists, otherwise send back original $var
    if ( ! ($initial = Kohana::config($this->_layout_config.$type)))
      return $var;

    return array_merge($initial, $var);
  }
  
}

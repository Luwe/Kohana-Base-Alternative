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
   * Holds the config array
   * @var  array
   */
  public $config;

  /**
   * Config file to get initial layout settings from
   * @var  string
   */
  protected $_config_file = 'website';

  /**
   * Overloaded render method to include config setting and pre_rendering
   *
   * @return  string
   */
  public function render()
  {
    $this->config = Kohana::$config->load($this->_config_file)->as_array();
    $this->pre_render();

    return parent::render();
  }

  /**
   * Pre-render method
   *
   * @return  void
   */
  public function pre_render()
  {
    // Everything that needs to happen after config, but before rendering
  }

}

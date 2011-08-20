<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Highest level template shell. Uses a default template for entire website and
 * sets all default attributes.
 * 
 * Can only be extended
 * 
 * @package    LJCore
 * @author     Lieuwe Jan Eilander
 * @copyright  (c) 2010-2011 Lieuwe Jan Eilander
 */
abstract class Ljcore_View_Default extends View_Core {

  /**
   * Config file to get initial layout settings from
   * @var  string
   */
  protected $_config_file = 'website';

  /**
   * Holds the website layout template (set by controller)
   * @var  string  
   */
  protected $_layout;  
  
  /**
   * Array of javascript files relative to the js mediapath in config
   * @var  array
   */
  protected $_scripts = array();
  
  /**
   * Array of stylesheet files relative to the css mediapath in config (file.css => media)
   * @var  array
   */
  protected $_stylesheets = array();
  
  /**
   * Set global website title
   * 
   * @return  string
   */
  public function title()
  {
    return (string) $this->config['title'];
  } 
  
  /**
   * Basic favicon, override if not used as described here
   * 
   * @return  string
   */
  public function favicon()
  {
    return URL::site(Kohana::$config->load('media')->get('images')
      .$this->config['favicon'], NULL, FALSE);
  }
  
  /**
   * Transform self::$_scripts array for use in template
   * 
   * @return  array
   */
  public function scripts()
  {
    // Add initial global scripts
    $raw = $this->config['files']['js'] + $this->_scripts;
    $scripts = array();
    
    foreach ($raw as $file)
    {
      $file = $this->_format_media_uri($file, 'js');
      $scripts[] = $file;
    }
 
    return $scripts;
  }
  
  /**
   * Transform self::$_stylesheets array for use in template
   * 
   * @return  array
   */
  public function stylesheets() 
  {
    // Add initial global stylesheets
    $raw = $this->config['files']['css'] + $this->_stylesheets;
    $stylesheets = array();
    
    foreach ($raw as $stylesheet)
    {      
      $file = $this->_format_media_uri($stylesheet['file'], 'css');
      $stylesheets[] = array('file' => $file, 'media' => $stylesheet['media']);
    }
     
    return $stylesheets;    
  }
  
  /**
   * Format js and css files so they return the correct absolute url
   * 
   * @param  array  formatted urls
   */
  protected function _format_media_uri($file, $extension = 'css')
  {
    // Check if filename has extension
    if ( ! strchr($file, '.'.$extension))
    {
      $file .= '.'.$extension;
    }
    
    // Check if filename has protocol
    if ( ! strchr($file, '://'))
    {
      $file = URL::site(Kohana::$config->load('media')->get($extension).$file, NULL, FALSE);
    }
    
    return $file;
  }

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

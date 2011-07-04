<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Highest level controller shell for a basic website.
 * 
 * Default controller which can be used for initial requests, but also for
 * subrequests within an HMVC structure or AJAX widgets. The response body
 * will be sent back in the format provided by the accept header.
 * 
 * @todo       Use an array for setting auto_render and allowed request types per action
 *             Instead of globally for the entire controller. Otherwise, the before
 *             method needs to be overridden every time.
 * @uses       Modules/Kostache v3.1.x <git://github.com/zombor/KOstache.git>
 * @package    LJCore
 * @author     Lieuwe Jan Eilander
 * @copyright  (c) 2010-2011 Lieuwe Jan Eilander
 */
abstract class Ljcore_Controller_Default extends Controller {

  /**
   * Set flags for dealing with initial, sub and ajax requests to this controller
   */
  const REQUEST_INITIAL  = 1;
  const REQUEST_SUB      = 2;
  const REQUEST_AJAX     = 3;

  /**
   * Holds the view object for the current request
   * @var  Kostache
   */
  public $view;

  /**
   * Holds the current session
   * @var  Kohana_Session
   */
  public $session;
  
  /**
   * Autorender request response
   * @var  boolean
   */
  public $auto_render = TRUE;
  
  /**
   * Is set to the used request type as provided by the REQUEST_ flags
   * @var  integer
   */
  protected $_request_type;
  
  /**
   * Allow or disallow initial requests being made to this controller
   * @var  array
   */
  protected $_allow_initial_request = TRUE;
   
  /**
   * Hold the response format for this request
   * @var  string  
   */
  protected $_response_format;

  /**
   * Supported output formats for this controller (specify subdir where templates are found + header accept-type)
   * 
   *   '.format' => array(layout, subtemplate, type)
   *
   * @var  array
   */
  protected $_accept_formats = array(
    '.html' => array('layout' => 'default', 'subtemplate' => NULL, 'type' => 'text/html'),
    '.json' => array('layout' => 'json', 'subtemplate' => 'json', 'type' => 'application/json'),
  );
  
  /**
   * Method which is executed before any action takes place   
   * 
   * @return  void 
   * @uses    Arr
   * @uses    Kohana::config()
   * @throws  Http_Exception_403  if initial requests to this controller are not allowed
   * @throws  Http_Exception_415  if response-format is not supported by this controller
   */
  public function before()
  {
    // Execute parent method
    parent::before();

    // Check if we're dealing with an initial, sub or ajax request
    if ($this->request->is_ajax()) 
    {
      $this->_request_type = self::REQUEST_AJAX;
    }
    elseif ($this->request->is_initial()) 
    {
      // Check if initial requests are allowed
      if ($this->_allow_initial_request !== TRUE)
        throw new Http_Exception_403('Initial requests are forbidden');

      $this->_request_type = self::REQUEST_INITIAL;
    }
    else
    {
      $this->_request_type = self::REQUEST_SUB;
    }
    
    if ($this->auto_render === TRUE)
    {
      // Throw exception if none of the accept-types is not supported
      $format = $this->request->param('format');

      if ( ! isset($this->_accept_formats[$format]))
        throw new Http_Exception_415('Unsupported accept-type', NULL);
    
      // Set response type
      $this->_response_format = $this->_accept_formats[$format];

      // Set path to view class
      $directory = Request::current()->directory() ? Request::current()->directory().'/' : '';
      $view_path = $directory.Request::current()->controller().'/'.Request::current()->action();
      $view_path = strtolower($view_path);
      
      // Set view object
      $this->view = $this->_prepare_view($view_path);
    }

    // Initialize session (default adapter is database)
    $this->session = Session::instance();
  }
  
  /**
   * Method which is executed after any action
   * 
   * @return  void
   * @throws  Http_Exception_404  if view not found 
   */
  public function after()
  {
    if ($this->auto_render === TRUE)
    {
      // Check if view object is not empty
      if ($this->view === NULL)
        throw new Http_Exception_404('Page not found');
      
      // Set header content-type to response format
      $this->response->headers('Content-Type', $this->_response_format['type']);
      
      // Set response body
      $this->response->body($this->view);
    }
    
    // Execute parent method
    parent::after();
  }
  
  /**
   * Method to ensure Views use the correct response format for the request
   * 
   * @param   string   Requested view path
   * @param   string   Requested response format
   * @return  mixed
   */
  protected function _prepare_view($view_path)
  { 
    // Setup view class
    $class = 'View_'.str_replace('/', '_', $view_path);

    // Setup full path to template for the view class
    $full_path = trim($view_path.'/'.$this->_response_format['subtemplate'], '/');

    try
    {
      // try to get the View class
      if ( ! class_exists($class))
        return NULL;

      $view = new $class($full_path);
      $view->set('_layout', $this->_response_format['layout']);
    }
    catch (Kohana_Exception $e)
    {
      // Try to send back the bare template (useful for static content)
      $view = ($file = Kohana::find_file('templates', $full_path, 'mustache')) ? file_get_contents($file) : NULL;
    }
    return $view;
  }

  /**
   * Redirect function implementing reverse routing
   *
   * @param   string  Route name to be used for reverse routing
   * @param   string  Request controller
   * @param   string  Request action
   * @param   array   Overflow (for instance: directory or id to be added to the uri)
   * @return  void
   */
  protected function _redirect($route, $controller = 'home', $action = 'index', array $overflow = array())
  {
    // Add components for uri
    $components = array(
      'controller' => (string) $controller,
      'action'     => (string) $action,
    );
    $components += $overflow;

    // Create uri using components
    $uri = Route::get($route)->uri($components);

    // Redirect
    $this->request->redirect($uri);
  }
  
}

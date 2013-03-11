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
   * @var  View
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
      // Set path to view class
      $directory = Request::current()->directory() ? Request::current()->directory().'/' : '';
      $view_path = $directory.Request::current()->controller().'/'.Request::current()->action();
      
      $class = 'View_'.str_replace('/', '_', $view_path);
      if ( ! class_exists($class))
        throw new Http_Exception_404('Page not found');

      // Set view object
      $this->view = $class;
    }

    // initialize session (default adapter is database)
    $this->session = session::instance();
  }
  
  /**
   * method which is executed after any action
   * 
   * @return  void
   * @throws  http_exception_404  if view not found 
   */
  public function after()
  {
    if ($this->auto_render === true)
    {
      // check if view object is not empty
      if ($this->view === null)
        throw new http_exception_404('page not found');
      
      // set response body
      $this->response->body(Kostache_Layout::factory()->render(new $this->view));
    }
    
    // execute parent method
    parent::after();
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
    $this->redirect($uri);
  }
  
}

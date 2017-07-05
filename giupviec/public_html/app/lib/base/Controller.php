<?php
namespace app\lib\base;

class Controller
{
    /**
     * @var View
     */
    public $view = null;
    /**
     * @var Request
     */
	protected $_request = null;
	// the current action
	protected $_action = null;
	
	protected $_namedParameters = array();

    protected $_viewPath = '';
    /**
     * @var Router
     */
    protected $router;

    public function __construct()
    {

    }

	/**
	 * initializes various things in the controller
	 */
	public function init()
	{
		$this->view = new View($this);
		
		$this->view->settings->action = $this->_action;
		$this->view->settings->controller = strtolower(str_replace('Controller', '', get_class($this)));
	}
	
	/**
	 * These filters are run BEFORE the action is run
	 */
	public function beforeFilters()
	{
		// no standard filers
	}
	
	/**
	 * These filters are run AFTER the action is run
	 */
	public function afterFilters()
	{
		// no standard filers
	}
	
	/**
	 * The main entry point into the controller execution path. The parameter 
	 * taken is the action to execute.
	 * @param string $action the action to execute
	 * @throws \Exception
	 */
	public function execute($action = 'index')
	{
		// stores the current action
		$this->_action = $action;

		// initializes the controller
		$this->init();
		
		// executes the before filters
		$this->beforeFilters();
		
		// adds the action suffix to the function to call
		$actionToCall = $action.'Action';

		// executes the action
		$this->$actionToCall();

		// executes the after filterss
		$this->afterFilters();


	}

    public function render($view, $data = [])
    {
        $this->view->render($this->_viewPath.$view, $data);
	}

    /**
     * @param string $viewPath
     */
    public function setViewPath($viewPath)
    {
        $this->_viewPath = $viewPath;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param mixed $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
	 * fetches the view script for the given action
	 * @param string $action
	 * @return string the path to the view script
	 */
	protected function _getViewScript($action)
	{
		// fetches the current controller executed
		$controller = get_class($this);
		// removes the "Controller" part and adds the action name to the path
		$script = strtolower(substr($this->getControllerName(), 0, -10) . '/' . $action . '.phtml');
		// returns the script to render
		return $script;
	}

	public function getControllerName()
    {
        return substr(strrchr(get_class($this), '\\'), 1);
    }
	
	/**
	 * The base url is used if the application is located in a subfolder. Use
	 * this function when linking to things.
	 * @return string the baseUrl for the application.
	 */
	protected function _baseUrl()
	{
		return WEB_ROOT;
	}
	
	/**
	 * Fetches the current request
	 * @return Request
	 */
	public function getRequest()
	{
		// initializes the request object
		if ($this->_request == null) {
			$this->_request = new Request();
		}
		
		return $this->_request;
	}
	
	/**
	 * A way to access the current request parameters
	 * @param string $key the key to look for
	 * @param mixed $default the default value, else null
	 * @return mixed
	 */
	protected function _getParam($key, $default = null)
	{
		// tests against the named parameters first
		if (isset($this->_namedParameters[$key])) {
			return $this->_namedParameters[$key];
		}
		
		// tests against the GET/POST parameters
		return $this->getRequest()->getParam($key, $default);
	}
	
	/**
	 * Fetches all the current parameters
	 * @return array a list of all the parameters
	 */
	protected function _getAllParams()
	{
		return array_merge($this->getRequest()->getAllParams(), $this->_namedParameters);
	}
	
	public function addNamedParameter($key, $value)
	{
		$this->_namedParameters[$key] = $value;
	}

	protected function redirect($url, $statusCode = 302)
    {
        header('Location: ' . BASE_URL . ltrim($url, '/'), true, $statusCode);
        die();
    }
}

<?php
namespace app\lib\base;

class View
{
    // used for holding the content of the view script
    protected $_content = "";
    // the standard layout
    protected $_layout = 'layout';

    protected $_viewEnabled = true;
    protected $_layoutEnabled = true;

    // initializes the data array
    protected $_data = array();
    // intializes the additional javascripts to add to the header.
    protected $_javascripts = '';

    public $_styles = [];

    public $settings = null;

    protected $controller;

    public $title = '';

    public $description = '';

    public function __construct($controller)
    {
        $this->settings = new \stdClass();
        $this->controller = $controller;
    }

    /**
     * Renders the view script, and stores the output
     */
    protected function _renderViewScript($viewScript)
    {

        // starts the output buffer
        ob_start();
        $view = ROOT_PATH . 'app/views/' . $viewScript;

        if (is_file($view)) {

            include($view);
        }

        // includes the view script
        // returns the content of the output buffer
        $this->_content = ob_get_clean();
    }
    
    /**
     * Fetches the content of the current view script
     */
    public function content()
    {
        return $this->_content;
    }

    /**
     * Renders the current view.
     */
    public function render($viewScript, $data = [])
    {

        $this->_data = $data;
        if ($viewScript && $this->_viewEnabled) {
            // renders the view script
            $this->_renderViewScript($viewScript. '.phtml');
        }

        if ($this->_isLayoutDisabled()) {
            echo $this->_content;
        } else {
            // includes the current view, which uses the "$this->content()" to output the

            // view script that was just rendered
            $layout = ROOT_PATH . '/app/views/layouts/' . $this->_getLayout() . '.phtml';

            include($layout);
        }
    }

    public function renderPartial($viewScript)
    {
        $view = ROOT_PATH . 'app/views/' . $viewScript. '.phtml';
        include($view);
    }

    /**
     * Renders the given data as json
     * @param mixed $data
     */
    public function renderJson($data)
    {
        $this->disableView();
        $this->disableLayout();

        // sets the json headers
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        echo json_encode($data);
    }

    protected function _getLayout()
    {
        return $this->_layout;
    }

    public function setLayout($layout)
    {
        $this->_layout = $layout;
        if ($layout) {
            $this->_enableLayout();
        }
    }

    public function disableLayout()
    {
        $this->_layoutEnabled = false;
    }

    public function disableView()
    {
        $this->_viewEnabled = false;
    }

    /**
     * stores the given data on the given key
     * @param string $key the key to store the data under
     * @param mixed $value the value to store
     */
    public function __set($key, $value)
    {
        // stores the data
        $this->_data[$key] = $value;
    }

    /**
     * Returns the data if it exists, else nul
     * @param string $key the data to look for
     * @return mixed the data found or null
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }

        return null;
    }

    /**
     * The base url is used if the application is located in a subfolder. Use
     * this function when linking to things.
     * @return string the baseUrl for the application.
     */
    public function baseUrl()
    {
        return WEB_ROOT;
    }

    /**
     * Adds a new javascript to the header.
     * @param string $script the path to the script to add
     */
    public function addJsFile($script)
    {
        $this->_javascripts .= '<script type="text/javascript" src="' . BASE_URL . $script . '"></script>' . "\n";
    }

    public function addJs($script)
    {
        $this->_javascripts .= "<script type=\"text/javascript\">{$script}</script>" . "\n";
    }

    /**
     * Prints the included javascripts
     */
    public function printScripts()
    {
        echo $this->_javascripts;
    }

    /**
     * Sets the layout to be used
     */
    protected function _enableLayout()
    {
        $this->_layoutEnabled = true;
    }

    /**
     * Tests if the layout is disabled
     */
    protected function _isLayoutDisabled()
    {
        return !$this->_layoutEnabled;
    }

    /**
     * @return array
     */
    public function outputCss()
    {
        foreach ($this->_styles as $style) {
            echo "<link href='". BASE_URL.$style ."' media='all' rel='stylesheet' type='text/css' />";
        }
    }

    /**
     * @param string $styles
     */
    public function addCssFile($styles)
    {
        $this->_styles[] = $styles;
    }
}

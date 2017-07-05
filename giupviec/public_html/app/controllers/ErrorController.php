<?php
namespace app\controllers;
use app\lib\base\Controller;

/**
 * A controller used for handling standard errors
 * @author jimmiw
 * @since 2012-06-27
 */
class ErrorController extends ControllerBase
{

    /**
     * @var \Exception
     */
    protected $_exception = null;
	
	/**
	 * Sets the exception to show information about
	 */
	public function setException(\Exception $exception)
	{
		$this->_exception = $exception;
	}

    public function setLayout($layout)
    {
        $this->view->setLayout($layout);
	}
	
	/**
	 * The error action, which is called whenever there is an error on the site
	 */
	public function errorAction()
	{
		// sets the 404 header
		header("HTTP/1.0 404 Not Found");
		
		// sets the error to be rendered in the view
		$error = $this->_exception->getMessage();

        $this->render('error/error', ['error' => $error]);
		// logs the error to the log
		error_log($error);
		error_log($this->_exception->getTraceAsString());
	}
}

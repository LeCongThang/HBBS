<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/22/2016
 * Time: 1:47 PM
 */

namespace app\controllers\admin;


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

    /**
     * The error action, which is called whenever there is an error on the site
     */
    public function errorAction()
    {

        // sets the 404 header
        header("HTTP/1.0 404 Not Found");

        // sets the error to be rendered in the view
        $this->view->error = $this->_exception->getMessage();

        $this->view->setLayout('admin/main');
        $this->setViewPath('admin/');
        $this->render('error');
        // logs the error to the log

        error_log($this->view->error);
        error_log($this->_exception->getTraceAsString());
    }
}
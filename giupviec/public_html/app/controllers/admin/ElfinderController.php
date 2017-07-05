<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 2:56 PM
 */

namespace app\controllers\admin;


class ElfinderController extends ControllerBase
{
    public function init()
    {
        parent::init();
        $this->view->disableLayout();
    }
    public function indexAction()
    {
        $this->view->render('admin/elfinder/index');
    }

    public function connectorAction()
    {
        $this->view->render('admin/elfinder/connector');
    }

    public function galleryAction()
    {
        $this->view->render('admin/elfinder/gallery');
    }

    public function connectorgalleryAction()
    {
        $this->view->render('admin/elfinder/connector-gallery');
    }
}
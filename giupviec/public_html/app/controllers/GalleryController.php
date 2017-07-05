<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 1/11/2017
 * Time: 9:29 AM
 */

namespace app\controllers;


use app\helpers\Paginator;
use app\models\Album;
use app\models\Gallery;

class GalleryController extends ControllerBase
{
    public function imageAction()
    {
        $where = ' WHERE type = ' . Album::TYPE_IMAGE;
        $total = Album::model()->count($where);
        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'gallery?page=' . Paginator::NUM_PLACEHOLDER);
        $pages->setItemsPerPage(9);
        $models = Album::model()->fetchAll($where . " ORDER BY id DESC " . $pages->getSql());
        $this->render('gallery/image', ['images' => $models]);
    }

    public function imagesAction()
    {
        $slug = $this->_getParam('slug');

        $model = Album::model()->one(" WHERE slug = '{$slug}'");

        if (!$model) {
            throw new \Exception('Page not found');
        }
        switch ($model->type) {
            case Album::TYPE_VIDEO:
             return  $this->showVideos($model);
        }
        $dir = ROOT_PATH . 'images/gallery/' . $slug . '/';
        $images = [];
        if (is_dir($dir)) {
            $_images = glob($dir . "*.{jpg,gif,png,JPG}", GLOB_BRACE);
            foreach ($_images as $img) {
                $images[] = $slug . '/' . basename($img);
            }
        }
        $this->render('gallery/image-list', ['images' => $images, 'model' => $model]);
    }

    public function videoAction()
    {
        $where = ' WHERE type = ' . Album::TYPE_VIDEO;

        $total = Album::model()->count($where);
        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'gallery?page=' . Paginator::NUM_PLACEHOLDER);
        $pages->setItemsPerPage(9);
        $models = Album::model()->fetchAll($where . " ORDER BY id DESC " . $pages->getSql());
        $this->render('gallery/image', ['images' => $models]);
    }

    public function showVideos($model)
    {
        $where = " where album_id = ".$model->id;
        $total = Gallery::model()->count($where);
        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'video?page=' . Paginator::NUM_PLACEHOLDER);
        $pages->setItemsPerPage(9);
        $models = Gallery::model()->fetchAll($where." ORDER BY id DESC " . $pages->getSql());
        $this->render('gallery/video', ['models' => $models, 'pages' => $pages, 'model' => $model]);
    }
}
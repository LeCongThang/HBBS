<?php
/**
 * Created by PhpStorm.
 * User: Phan
 * Date: 12/27/2016
 * Time: 2:35 PM
 */

namespace app\controllers;

use app\helpers\ArrayHelper;
use app\helpers\Paginator;
use app\models\Category;
use app\models\Idol;
use app\models\News;
use app\models\Post;

class NewsController extends ControllerBase
{
    public function indexAction()
    {
        $slug = $this->_getParam('slug');
        $where = '';
        if ($slug) {
            if (preg_match('/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/', $slug)) {
                $category = Category::model()->one("WHERE slug = '{$slug}'");
                if ($category) {
                    $where = " AND category_id = {$category->id}";
                }
            }
        }
        $news = Post::model()->fetchAll(" WHERE type = 'news' and status = 1 ORDER BY id desc LIMIT 5");
        $ids = ArrayHelper::map($news, 'id', 'id');
        if (!empty($ids)) {
            $where .= " AND id not in (" . implode(", ", $ids) . ")";
        }
        $total = News::model()->count($where);
        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'category/'.$slug.'?page=' . Paginator::NUM_PLACEHOLDER);
        $pages->setItemsPerPage(6);
        $models = News::model()->fetchAll("{$where} AND status = 1 ORDER BY id desc " . $pages->getSql());
        $this->render('news/index', ['models' => $models, 'pages' => $pages, 'news' => $news]);
    }

    public function detailAction()
    {
        $slug = $this->_getParam('slug');
        $model = News::model()->one(" WHERE slug = '{$slug}'");
        if (!$model) {
            throw new \Exception('Page not found');
        }
        $type = "type = 'news'";
        $view = 'detail';
        if ($model->type == 'intro') {
            $type = "type = 'intro'";
            $view = 'intro';
        }
        $total = Post::model()->count(" WHERE {$type} AND id <> {$model->id} AND status = 1");
        $pages = new Paginator($total, $this->getRequest()->get('page', 1), $slug . '?page=' . Paginator::NUM_PLACEHOLDER);
        $pages->setItemsPerPage(10);
        $modelsRelated = Post::model()->fetchAll(" WHERE {$type} AND id <> {$model->id} AND status = 1 ORDER BY id desc ".$pages->getSql() , true);

        $this->render('news/' . $view, ['model' => $model, 'modelsRelated' => $modelsRelated, 'pages' => $pages]);
    }

    public function idolAction()
    {
        $slug = $this->_getParam('slug');
        $model = Idol::model()->one(" WHERE slug = '{$slug}'");
        if (!$model) {
            throw new \Exception('Page not found');
        }
        $total = Idol::model()->count(" WHERE id <> {$model->id} AND status = 1");
        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'idol/' . $slug . '?page=' . Paginator::NUM_PLACEHOLDER);
        $pages->setItemsPerPage(10);
        $modelsRelated = Idol::model()->fetchAll(" WHERE id <> {$model->id} AND status = 1 ORDER BY id desc " . $pages->getSql(), true);

        $this->render('news/idol', ['model' => $model, 'modelsRelated' => $modelsRelated, 'pages' => $pages]);
    }
}
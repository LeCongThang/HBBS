<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 1:23 PM
 */

namespace app\controllers\admin;

use app\lib\helpers\Slug;
use app\lib\web\FlashMessages;
use app\models\Category;

class CategoryController extends ControllerBase
{
    public function indexAction()
    {
        $models = Category::all();

        $this->view->render("admin/category/index", ['models' => $models]);
    }

    public function createAction()
    {
        $model = new Category();
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['title', 'title_en', 'description', 'description_en', 'status']);
            $slug = new Slug();
            $data['slug'] = $slug->createSlug('categories', $data['title']);
            $model->save($data);
            FlashMessages::success('Thêm danh mục thành công.');
            $this->redirect('admin/category');

        }

        $this->view->render("admin/category/create", ['model' => $model]);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Category::model()->fetchOne($id);

        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['title', 'title_en', 'description', 'description_en', 'status']);
            $slug = new Slug();
            $data['slug'] = $slug->updateSlug('posts', $data['title'], $model->id);
            if ($model->save($data)) {
                FlashMessages::success('Cập nhật danh mục thành công.');
                $this->redirect('admin/category');
            }
            $model->map($data);

        }

        $this->view->render("admin/category/create", ['model' => $model]);
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        Category::model()->delete($id);
        $this->redirect('admin/category');
    }
}
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
use app\models\Introduce;

class IntroduceController extends ControllerBase
{
    public function indexAction()
    {
        $where = " where type = '".Introduce::TYPE . "'";
        $models = Introduce::model()->fetchAll($where);

        $this->view->render("admin/introduce/index", ['models' => $models]);
    }

    public function createAction()
    {
        $model = new Introduce();
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['title', 'title_en', 'content', 'content_en', 'introduce', 'introduce_en', 'status']);
            $data['type'] = Introduce::TYPE;
            $slug = new Slug();
            $data['slug'] = $slug->createSlug('posts', $data['title']);
            $data['status'] = $this->getRequest()->getParam('status', 0);
            if (($id = $model->save($data))) {
                FlashMessages::success('Thêm bài viết thành công.');
                $this->redirect('admin/introduce/update?id='.$id);
            }
        }

        $this->view->render("admin/introduce/create", ['model' => $model]);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Introduce::model()->fetchOne($id);

        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['title', 'title_en', 'content', 'content_en', 'introduce', 'introduce_en', 'status']);
            $slug = new Slug();
            $data['slug'] = $slug->updateSlug('posts', $data['title'], $model->id);
            $data['status'] = $this->getRequest()->getParam('status', 0);
            if ($model->save($data)) {
                $model->map($data);
                FlashMessages::success('Cập nhật bài viết thành công.');
            }
        }
        $this->view->render("admin/introduce/create", ['model' => $model]);
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        Introduce::model()->delete($id);
        $this->redirect('admin/introduce');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 1:23 PM
 */

namespace app\controllers\admin;

use app\lib\helpers\ImageResizer;
use app\lib\helpers\Slug;
use app\lib\web\FlashMessages;
use app\models\Idol;
use Gregwar\Image\Image;

class IdolController extends ControllerBase
{
    public function indexAction()
    {
        $models = Idol::all();

        $this->view->render("admin/idol/index", ['models' => $models]);
    }

    public function createAction()
    {
        $model = new Idol();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->only(['name', 'name_en', 'introduce', 'introduce_en', 'content', 'content_en', 'name', 'status']);
            $image = $this->getRequest()->getFile('image');
            $slug = new Slug();
            $data['slug'] = $slug->createSlug('idols', $data['name']);
            $newName = $data['slug'] . '.' . $image->ext;
            if ($image->save(Idol::UPLOAD_PATH, $newName) !== false) {
                Image::open(Idol::UPLOAD_PATH . $newName)
                    ->zoomCrop(500, 630, 0xffffff)
                    ->save(Idol::UPLOAD_PATH . $newName);
                $data['image'] = Idol::UPLOAD_PATH . $newName;
                if ($model->save($data)) {
                    FlashMessages::success('Thêm hình tượng thành công!');
                    $this->redirect('admin/idol');
                }
            } else {
                $model->map($data);
                FlashMessages::error('Vui lòng chọn ảnh đại diện.');
            }
        }
        $this->view->render("admin/idol/create", ['model' => $model]);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Idol::model()->fetchOne($id);

        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['name', 'name_en', 'introduce', 'introduce_en', 'content', 'content_en', 'name', 'status']);
            $image = $this->getRequest()->getFile('image');
            if ($image) {
                $newName = $model->slug . '.' . $image->ext;
                if ($image->save(Idol::UPLOAD_PATH, $newName) !== false) {
                    Image::open(Idol::UPLOAD_PATH . $newName)
                        ->zoomCrop(500, 630, 0xffffff)
                        ->save(Idol::UPLOAD_PATH . $newName);
                    $data['image'] = Idol::UPLOAD_PATH . $newName;
                }
            }
            $data['status'] = $this->getRequest()->getParam('status', 0);
            if ($model->save($data)) {
                FlashMessages::success('Cập nhật hình tượng thành công!');
                $this->redirect('admin/idol');
            }
            $model->map($data);
        }

        $this->view->render("admin/idol/create", ['model' => $model]);
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        Idol::model()->delete($id);

        $this->redirect('admin/idol');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Phan
 * Date: 12/26/2016
 * Time: 1:24 PM
 */

namespace app\controllers\admin;

use app\lib\web\FlashMessages;
use app\models\Slider;
use Gregwar\Image\Image;

class SliderController extends ControllerBase
{
    public function indexAction()
    {
        $banners = Slider::model()->fetchAll();

        $this->render('slider/index', ['banners' => $banners]);
    }

    public function createAction()
    {
        $model = Slider::model();
        if ($this->getRequest()->isPost()) {
            $file = $this->getRequest()->getFile('image');
            $data = [];
            $filename = $model->id.''.$file->name;
            if ($file && $file->save(Slider::UPLOAD_PATH, $filename)) {
                Image::open(Slider::UPLOAD_PATH.$filename)
                    ->zoomCrop(1170, 450, 0xffffff)
                    ->save(Slider::UPLOAD_PATH.$filename);
                $data['image'] = Slider::UPLOAD_PATH.$filename;
            }

            $data['status'] = $this->getRequest()->getParam('status', false);
            if ($model->save($data)) {
                FlashMessages::success('Cập nhật banner thành công!');
            }
        }

        $this->render('slider/update', ['model' => $model]);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Slider::model()->fetchOne($id);
        if (!$model) {
            throw new \Exception('Id không tồn tại');
        }

        if ($this->getRequest()->isPost()) {
            $file = $this->getRequest()->getFile('image');
            $data = [];
            $filename = $model->id.''.$file->name;
            if ($file && $file->save(Slider::UPLOAD_PATH, $filename)) {
                Image::open(Slider::UPLOAD_PATH.$filename)
                    ->zoomCrop(1170, 450, 0xffffff)
                    ->save(Slider::UPLOAD_PATH.$filename);
                $data['image'] = Slider::UPLOAD_PATH.$filename;
            }

            $data['status'] = $this->getRequest()->getParam('status', false);
            if ($model->save($data)) {
                FlashMessages::success('Cập nhật banner thành công!');
                $this->redirect('admin/slider');
            }
        }

        $this->render('slider/update', ['model' => $model]);
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Slider::model()->fetchOne($id);
        if (is_file($model->image)) {
            unlink($model->image);
        }
        FlashMessages::success('Xóa thành công!');
        Slider::model()->delete($id);
        $this->redirect('admin/slider');
    }
}
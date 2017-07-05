<?php
/**
 * Created by PhpStorm.
 * User: Phan
 * Date: 12/26/2016
 * Time: 1:24 PM
 */

namespace app\controllers\admin;

use app\lib\web\FlashMessages;
use app\models\Banner;

class BannerController extends ControllerBase
{
    public function indexAction()
    {
        $banners = Banner::model()->fetchAll();

        $this->render('banner/index', ['banners' => $banners]);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Banner::model()->fetchOne($id);
        if (!$model) {
            throw new \Exception('Id không tồn tại');
        }

        if ($this->getRequest()->isPost()) {
            $file = $this->getRequest()->getFile('image');
            $data = [];
            $filename = $model->id.''.$file->name;
            if ($file && $file->save(Banner::UPLOAD_PATH, $filename)) {
                $data['image'] = Banner::UPLOAD_PATH.$filename;
            }

            $data['status'] = $this->getRequest()->getParam('status', false);
            if ($model->save($data)) {
                FlashMessages::success('Cập nhật banner thành công!');
            }
        }

        $this->render('banner/update', ['model' => $model]);
    }
}
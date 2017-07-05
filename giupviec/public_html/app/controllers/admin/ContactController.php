<?php
/**
 * Created by PhpStorm.
 * User: Phan
 * Date: 12/26/2016
 * Time: 3:50 PM
 */

namespace app\controllers\admin;


use app\lib\web\FlashMessages;
use app\models\Post;
use app\models\Setting;

class ContactController extends ControllerBase
{
    public function indexAction()
    {
        $model = Post::model()->fetchOne(1);
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->only(['title', 'title_en', 'content', 'content_en']);
            $map = $this->getRequest()->only(['address']);

            Setting::model()->update('map', $map['address']);
            if (($id = $model->save($data))) {
                FlashMessages::success('Cập nhật trang liên hệ thành công');
            }
        }

        $map = Setting::model()->one("WHERE `name` = 'map'", true);

        $this->render('page/contact', ['model' => $model, 'map' => $map['value']]);
    }
}
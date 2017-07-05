<?php
/**
 * Created by PhpStorm.
 * User: Phan
 * Date: 12/26/2016
 * Time: 10:03 AM
 */

namespace app\controllers\admin;


use app\lib\web\FlashMessages;
use app\models\Menu;

class MenuController extends ControllerBase
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $data = Menu::model()->fetchAll(' ORDER BY stt asc', true);
        $menus = Menu::model()->buildTree($data);
        if ($request->isPost()) {
            //
            $oldIds = array_column($data, 'id');
            $post = $request->getParam('menus');
            //submit ids
            $ids = array_column($post, 'id');
            $deletes = array_diff($oldIds, $ids);
            if (!empty($deletes)) {
                Menu::model()->getDb()->deletes('menus',$deletes);
            }

            foreach ($post as $item) {

                $model = Menu::model();
                if ($item['id'] > 0) {
                    $model->id = $item['id'];
                } else {
                    unset($item['id']);
                }
                $item['stt'] = $item['id']?:0;

                try {
                    $model->save($item);
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                }
            }
            FlashMessages::success('Cập nhật menu thành công!');
            $this->redirect('admin/menu');
        }

        $this->render('menu/index', ['menus' => $menus]);
    }
}
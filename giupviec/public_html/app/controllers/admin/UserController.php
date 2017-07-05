<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 1:23 PM
 */

namespace app\controllers\admin;

use app\helpers\Paginator;
use app\lib\auth\AuthComponent;
use app\lib\web\FlashMessages;
use app\models\User;

class UserController extends ControllerBase
{
    public function indexAction()
    {
        $total = User::model()->count();
        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'admin/user?page='.Paginator::NUM_PLACEHOLDER);

        $models = User::model()->fetchAll($pages->getSql());
        $this->view->render("admin/user/index", ['models' => $models, 'pages' => $pages]);
    }

    public function createAction()
    {
        $model = new User();
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['username', 'email', 'fullname', 'position', 'status']);
            if ($model->checkUsernameExits($data['username'])) {
                $data['password'] = User::createPassword($this->getRequest()->getParam('_password'));
                $permissions = $this->_request->getParam('permissions');
                $data['role'] = json_encode($permissions);
                if (($id =$model->save($data))) {
                    FlashMessages::success('Thêm người dùng thành công!');
                    $this->redirect('admin/user/update?id='.$id);
                }
            } else {
                FlashMessages::error('Username đã tồn tại.');
            }
            $model->map($data);
        }

        $this->view->render("admin/user/create", ['model' => $model]);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = User::model()->fetchOne($id);

        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['username', 'email', 'fullname', 'position', 'status', 'role']);
            $password = $this->getRequest()->getParam('_password');

            if ($password != null) {
                $data['password'] = User::createPassword($password);
                var_dump($data['password']);
            }
            if ($model->checkUsernameExits($data['username'], $model->id)) {
                if ($model->save($data)) {
                    FlashMessages::success('Cập nhật thành công!');
                }
            } else {
                FlashMessages::error('Username đã tồn tại.');
            }

        }

        $this->view->render("admin/user/create", ['model' => $model]);
    }

    public function profileAction()
    {
        $model = User::model()->fetchOne($this->user->id);
        $errors = [];
        if ($this->getRequest()->isPost()) {
            if (isset($_POST['update-profile'])) {
                $data = $this->_request->only(['fullname', 'email', 'position']);
                $model->save($data);
                FlashMessages::success('Cập nhật thành công');
                $this->redirect('admin/user/profile');
            } elseif (isset($_POST['change-password'])) {
                $data = $this->_request->only(['old_password', 'password', 're_password']);
                if ($data['password'] !== $data['re_password']) {
                    $errors[] = 'Mật khẩu nhập lại không trùng khớp!';
                } else {
                    $auth = new AuthComponent();
                    if ($auth->validatePassword($data['old_password'], $model->password)) {
                        $model->save(['password' => User::createPassword($data['password'])]);
                        FlashMessages::success('Thay đổi mật khẩu thành công');
                    } else {
                        $errors[] = 'Mật khẩu hiện tại không đúng!';
                    }
                }
            }
        }

        $this->view->render("admin/user/profile", ['model' => $model, 'errors' => $errors]);
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        User::model()->delete($id);
        FlashMessages::success('Xóa user thành công');
        $this->redirect('admin/user');
    }
}
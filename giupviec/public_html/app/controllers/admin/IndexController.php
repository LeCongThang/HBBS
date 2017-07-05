<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 10:00 AM
 */

namespace app\controllers\admin;


use app\lib\auth\AuthComponent;
use app\models\Idol;
use app\models\News;
use app\models\Register;
use app\models\Setting;
use app\models\User;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $registerCount = Register::model()->count('WHERE status = ' . Register::STATUS_NEW);
        $total = Register::model()->count();
        $newsCount = News::model()->count();
        $idolCount = Idol::model()->count();
        $this->view->render('admin/index/dashboard', [
            'registerCount' => $registerCount,
            'total' => $total,
            'newsCount' => $newsCount,
            'idolCount' => $idolCount,
        ]);
    }

    public function loginAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $username = $request->getParam('username');
            $password = $request->getParam('password');
            $remember = $request->getParam('remember');
            $auth = new AuthComponent();
            if ($auth->auth($username, $password, $remember)) {
                $user = $auth->getUser();
                if ($user->role == User::USER) {
                    $this->redirect('admin/news');
                }
                $this->redirect('admin');
            }
        }

        $this->view->setLayout('admin/guest');
        $this->render('index/login');
    }

    public function logoutAction()
    {
        $auth = new AuthComponent();
        $auth->logout();
        $this->redirect('admin');
    }

    public function settingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('settings');
            foreach ($data as $name => $val) {
                $model = Setting::model();
                $model->name = $name;
                $model->save(['value' => $val]);
            }
        }

        $tmpSettings = Setting::model()->fetchAll('', true);
        $settings = [];
        foreach ($tmpSettings as $setting) {
            $settings[$setting['name']] = $setting['value'];
        }

        $this->view->render('admin/index/setting', [
            '_settings' => $settings
        ]);
    }
}
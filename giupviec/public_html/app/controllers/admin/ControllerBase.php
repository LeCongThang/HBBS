<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 10:57 AM
 */

namespace app\controllers\admin;


use app\lib\auth\AuthComponent;
use app\lib\base\Controller;
use app\models\User;

class ControllerBase extends Controller
{
    /**
     * @var User
     */
    public $user;

    protected $_viewPath = 'admin/';

    private $allowedUser = [
        NewsController::class => [
            'index', 'create', 'update', 'video', 'updateVideo'
        ],
        UserController::class => [
            'profile'
        ],
        IndexController::class => [
            'logout'
        ],
        ErrorController::class => [
            'error'
        ],
        ElfinderController::class => [
            'index', 'connector', 'gallery', 'connectorgallery'
        ],
    ];

    public function init()
    {
        parent::init();
        $this->view->setLayout('admin/main');
        $this->checkAuth();
    }

    private function checkAuth()
    {
        $auth = new AuthComponent();
        $this->user = $auth->getUser();
        if (!$this->user && $this->_action !== 'login') {
            $this->redirect('admin/login');
        }
        $controller = get_class($this);

        if ($this->user) {

            if ($this->user->role != User::USER && $this->user->role != User::SUPPER_USER) {
                $this->redirect('');
            }
            if ($this->user->role != User::SUPPER_USER) {
                $isAllowed = false;
                if (array_key_exists($controller, $this->allowedUser)) {

                    if (in_array($this->_action, $this->allowedUser[$controller])) {

                        $isAllowed = true;
                    }
                }
                if (!$isAllowed) {
                    throw new \Exception('Bạn không có quyền truy cập trang này');
                }
            }
        }

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 9:28 AM
 */

namespace app\controllers;

use app\helpers\EmailContact;
use app\helpers\Paginator;
use app\lib\auth\AuthComponent;
use app\lib\web\FlashMessages;
use app\models\Idol;
use app\models\News;
use app\models\Post;
use app\models\Program;
use app\models\ProgramItem;
use app\models\Register;
use app\models\RegisterCourse;
use app\models\RegisterProgram;
use app\models\Setting;
use app\models\User;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $news = Post::model()->fetchAll(" WHERE type = 'news' and status = 1 ORDER BY id desc LIMIT 5", true);
        $idols = Idol::model()->fetchAll(" WHERE status = 1 ORDER BY id desc LIMIT 12", true);

        $this->render('index/index', ['news' => $news, 'idols' => $idols]);
    }

    public function contactAction()
    {
        //get contact content
        $model = News::model()->fetchOne(1);
        $message = '';
        $error = false;
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->only(['name', 'phone', 'email', 'content']);
            $mailer = new EmailContact($data);
            if ($mailer->send()) {
                $message = lang('home.contact_thanks');
            } else {
                $message = lang('home.contact_error');
                $error = true;
            }
        }
        $setting = Setting::model()->one("WHERE `name` = 'map'", true);
        $this->render('index/contact', ['model' => $model, 'message' => $message, 'error' => $error, 'map' => $setting['value']]);
    }

    public function idolAction()
    {
        $total = Idol::model()->count();
        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'idols?page=' . Paginator::NUM_PLACEHOLDER);
        $pages->setItemsPerPage(6);
        $models = Idol::model()->fetchAll(" WHERE status = 1 ORDER BY id desc " . $pages->getSql(), true);
        $this->render('index/idols', ['models' => $models, 'pages' => $pages]);
    }

    public function programAction()
    {
        $slug = $this->_getParam('slug');
        $model = Program::model()->one(" WHERE slug = '{$slug}'");
        if (!$model) {
            throw new \Exception('Page not found');
        }

        if ($this->getRequest()->isPost()) {
            $error = false;
            $courses = $this->getRequest()->getParam('courses');
            $data = $this->getRequest()->only(['name', 'phone', 'email', 'gender', 'company', 'position', 'address']);
            $date = \DateTime::createFromFormat('d/m/Y', $this->getRequest()->getParam('birthday'));
            if ($date) {
                $data['birthday'] = date('Y-m-d', $date->getTimestamp());
            }
            $data['status'] = Register::STATUS_NEW;
            if ($courses == null) {
                $error = true;
                FlashMessages::error(lang('home.error_missing_programs'));
            }
            $register = Register::model();

            if (!$register->checkExits('email', $data['email'])) {
                $error = true;
                FlashMessages::error(lang('home.email_exits'));
            }

            if (!$register->checkExits('phone', $data['phone'])) {
                $error = true;
                FlashMessages::error(lang('home.phone_exits'));
            }

            if (!$error) {
                if (($id = $register->save($data))) {
                    $idsPrograms = implode(",", $courses);
                    $programs = ProgramItem::model()->fetchAll("WHERE id in ({$idsPrograms})", true);

                    foreach ($courses as $course) {
                        foreach ($programs as $item) {
                            if ($item['id'] == $course) {
                                $m = RegisterCourse::model();
                                $item = [
                                    'register_id' => $id,
                                    'program_id' => $item['program_id'],
                                    'item_id' => $item['id'],
                                    'start_date' => $item['start_date'],
                                    'end_date' => $item['end_date'],
                                ];
                                $m->save($item);
                                break;
                            }
                        }
                    }
                    FlashMessages::success(lang('home.success_register'));
                    $this->redirect('program/' . $slug);
                }
            }
        }
        $this->render('index/program', ['model' => $model, 'request' => $this->getRequest()]);
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
                FlashMessages::success('Đăng nhập thành công!');
                $this->redirect('/');
            } else {
                FlashMessages::error('Tên đăng nhập hoặc mật khẩu không đúng.');
            }
        }
        $this->render('index/login', ['request' => $request]);
    }

    public function logoutAction()
    {
        $auth = new AuthComponent();
        $auth->logout();
        $this->redirect('');
    }

    public function searchAction()
    {
        $q = $this->getRequest()->get('q', '');

        $models = Post::model()->fetchAll(" WHERE title" . cl() . " like '%$q%' and status = 1 and type = 'news'");
        $this->render('index/search', ['models' => $models, 'q' => $q]);
    }

    public function lichAction()
    {
        if (!$this->auth->isUser()) {
            FlashMessages::warning('Vui lòng đăng nhập để xem lịch học!');
            $this->redirect('/');
        }
        $user = $this->auth->getUser();
		
        $programs = RegisterCourse::model()->getSchedule($user->id);
        $this->render('index/schedule', ['programs' => $programs,  'user' => $user]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 1:23 PM
 */

namespace app\controllers\admin;

use app\helpers\DateHelper;
use app\helpers\Paginator;
use app\lib\web\FlashMessages;
use app\models\Register;
use app\models\RegisterCourse;
use app\models\User;

class RegisterController extends ControllerBase
{
    public function indexAction()
    {
        $total = Register::model()->count();
        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'admin/register?page=' . Paginator::NUM_PLACEHOLDER);
        $models = Register::model()->fetchAll(" ORDER BY id DESC " . $pages->getSql());
        $this->view->render("admin/register/index", ['models' => $models, 'pages' => $pages]);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Register::model()->fetchOne($id);
        $db = Register::model()->getDb();
        if ($this->getRequest()->isPost()) {
            $model->birthday = DateHelper::toSqlDate($this->_request->getParam('birthday'));

            if ($this->getRequest()->getParam('create_user')) {
                $user = User::model();
                $password = trim(str_replace('-', '', $model->birthday));
                $username = $user->createUsernameByEmail($model->email);
                $userId = $user->save([
                    'username' => $username,
                    'password' => User::createPassword($password),
                    'email' => $model->email,
                    'status' => 1,
                ]);
            }
            $data = $this->_request->only(['name', 'gender', 'address', 'email', 'phone', 'company', 'position', 'status']);
            if (isset($userId)) {
                $data['password'] = $password;
                $data['user_id'] = $userId;
            }
            $data['birthday'] = $model->birthday;
            if ($model->save($data)) {
                $courses = $this->getRequest()->getParam('course');

                foreach ($courses as $course) {
                    if (isset($course['course_id'])) {
                        $m = RegisterCourse::model();
                        if (isset($course['id'])) {
                            $m->id = $course['id'];
                        }
                        $item = [
                            'register_id' => $id,
                            'program_id' => $course['program_id'],
                            'item_id' => $course['course_id'],
                            'start_date' => DateHelper::toSqlDate($course['start_date']),
                            'end_date' => DateHelper::toSqlDate($course['end_date']),
                            'complete' => isset($course['complete']) ? 1 : 0,
                        ];
                        $m->save($item);
                    } else {
                        if (isset($course['id'])) {
                            RegisterCourse::model()->delete($course['id']);
                        }
                    }
                }
                FlashMessages::success("Lưu thông tin học viên thành công");
            }

            $model = Register::model()->fetchOne($id);
        }
        $user = User::model()->fetchOne($model->user_id);
        if (!$user) {
            $user = User::model();
        }
        $models = $db->query("SELECT programs.`title`, programs.id, programs.id as program_id FROM programs ");

        $courses = RegisterCourse::model()->getRegisterData($model->id);


        $this->view->render("admin/register/create", ['model' => $model, 'models' => $models, 'user' => $user, 'courses' => $courses]);
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        Register::model()->delete($id);
        $this->redirect('admin/register');
    }

    public function createAction()
    {
        $model = Register::model();
        $model->status = 1;
        $db = Register::model()->getDb();
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['name', 'gender', 'address', 'email', 'phone', 'company', 'position', 'status']);
            $data['birthday'] = DateHelper::toSqlDate($this->_request->getParam('birthday'));
            if ($this->getRequest()->getParam('create_user')) {
                $user = User::model();
                $password = trim(str_replace('-', '', $data['birthday']));
                $username = $user->createUsernameByEmail($data['email']);
                $userId = $user->save([
                    'username' => $username,
                    'password' => User::createPassword($password),
                    'email' => $data['birthday'],
                    'status' => 1,
                ]);
                if ($userId) {
                    $data['password'] = $password;
                    $data['user_id'] = $userId;
                }
            }
            $id = $model->save($data);
            if ($id) {
                $courses = $this->getRequest()->getParam('course');
                foreach ($courses as $course) {
                    if (isset($course['course_id'])) {
                        $m = RegisterCourse::model();
                        if (isset($course['id'])) {
                            $m->id = $course['id'];
                        }
                        $m = RegisterCourse::model();
                        $item = [
                            'register_id' => $id,
                            'program_id' => $course['program_id'],
                            'item_id' => $course['course_id'],
                            'start_date' => DateHelper::toSqlDate($course['start_date']),
                            'end_date' => DateHelper::toSqlDate($course['end_date']),
                            'complete' => isset($course['complete']) ? 1 : 0,
                        ];
                        $m->save($item);
                    }
                }
                /*$programs = $this->getRequest()->getParam('program_items');
                foreach ($programs as $program_id => $course_id) {
                    $m = RegisterProgram::model();
                    $item = ['register_id' => $id, 'program_id' => $program_id, 'course_id' => $course_id];
                    $m->save($item);
                }*/
                FlashMessages::success('Thêm học viên thành công');
                $this->redirect('admin/register');
            }
        }
        $user = User::model()->fetchOne($model->user_id);
        if (!$user) {
            $user = User::model();
        }

        $models = $db->query("SELECT programs.`title`, programs.id, programs.id as program_id FROM programs ");

        $courses = RegisterCourse::model()->getRegisterCreate();

        $this->view->render("admin/register/create", ['model' => $model, 'models' => $models, 'user' => $user, 'courses' => $courses]);
    }
}
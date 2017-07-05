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
use app\lib\helpers\Slug;
use app\lib\web\FlashMessages;
use app\models\Course;
use app\models\Program;
use app\models\ProgramItem;
use app\models\RegisterProgram;

class ProgramController extends ControllerBase
{
    public function indexAction()
    {
        $models = Program::model()->fetchAll();

        $this->view->render("admin/program/index", ['models' => $models]);
    }

    public function createAction()
    {
        $model = new Program();
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['title', 'title_en', 'content', 'content_en', 'short_description', 'short_description_en', 'status']);
            $slug = new Slug();
            $data['slug'] = $slug->createSlug('programs', $data['title']);
            $data['status'] = $this->getRequest()->getParam('status', 0);
            $id = $model->save($data);
            if ($id) {
                $items = $this->getRequest()->getParam('programs');
                if (!empty($items)) {
                    foreach ($items as &$item) {
                        $modelItem = ProgramItem::model();
                        $item['program_id'] = $id;
                        $item['start_date'] = DateHelper::toSqlDate($item['start_date'])?:date('Y').'-01-01';
                        $item['end_date'] = DateHelper::toSqlDate($item['end_date'])?:date('Y').'-01-01';
                        try {
                            $modelItem->save($item);
                        } catch (\Exception $e) {

                        }
                    }
                }
                FlashMessages::success('Thêm chương trình thành công.');
                $this->redirect('admin/program/update?id='.$id);
            }
        }

        $this->view->render("admin/program/create", ['model' => $model]);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Program::model()->fetchOne($id);
        $models = ProgramItem::model()->fetchAll('WHERE program_id = '.$model->id, true);
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['title', 'title_en', 'content', 'content_en', 'short_description', 'short_description_en', 'status']);
            $slug = new Slug();
            $data['slug'] = $slug->updateSlug('posts', $data['title'], $model->id);
            $data['status'] = $this->getRequest()->getParam('status', 0);
            $model->save($data);
            $items = $this->getRequest()->getParam('programs');
            if (!empty($models)) {
                $oldIds = array_column($models, 'id');
                //submit ids
                if (!empty($items)) {
                    $ids = array_column($items, 'id');
                } else {
                    $ids = [];
                }

                $deletes = array_diff($oldIds, $ids);
                if (!empty($deletes)) {
                    ProgramItem::model()->getDb()->deletes('program_items',$deletes);
                }
            }
            if (!empty($items)) {
                foreach ($items as &$item) {
                    $modelItem = ProgramItem::model();
                    if (isset($item['id']) && $item['id'] > 0) {
                        $modelItem->id = $item['id'];
                        unset($item['id']);
                    }
                    $item['program_id'] = $model->id;
                    $item['start_date'] = DateHelper::toSqlDate($item['start_date'])?:date('Y-m').'-01';
                    $item['end_date'] = DateHelper::toSqlDate($item['end_date'])?:date('Y-m').'-01';
                    try {
                        $modelItem->save($item);
                    } catch (\Exception $e) {
                        var_dump($e->getMessage());
                    }
                }
            }
            FlashMessages::success('Cập nhật chương trình thành công.');
            $this->redirect('admin/program/update?id='.$id);
        }

        $this->view->render("admin/program/create", ['model' => $model, 'models' => $models]);
    }

    public function courseAddAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Program::model()->fetchOne($id);
        if (!$model) {
            throw new \HttpException('Not found');
        }
        $models = ProgramItem::model()->fetchAll('WHERE program_id = '.$model->id, true);
        $this->view->render("admin/program/course-form", ['model' => $model, 'models'=>$models]);
    }

    public function courseAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Program::model()->fetchOne($id);
        if (!$model) {
            throw new \HttpException('Not found');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $course = new Course();
            $data = $request->only(['start_date', 'end_date', 'place', 'note']);
            if ($request->getParam('id')) {
                $course->id = $request->getParam('id');
            }
            $data['program_id'] = $id;
            $data['start_date'] = DateHelper::toSqlDate($data['start_date']);
            $data['end_date'] = DateHelper::toSqlDate($data['end_date']);

            if ($course->save($data)) {
                FlashMessages::success('Lưu khóa học thành công!');
            }
        }


        $where = " WHERE program_id = {$id}";
        $total = Course::model()->count($where);

        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'admin/course?page='.Paginator::NUM_PLACEHOLDER);

        $models = Course::model()->fetchAll($where." ORDER BY id DESC ".$pages->getSql());
        $this->view->render("admin/program/course", ['model' => $model, 'models' => $models, 'pages' => $pages]);
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        Program::model()->delete($id);
        $this->redirect('admin/program');
    }
}
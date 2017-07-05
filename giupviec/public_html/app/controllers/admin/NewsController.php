<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 10:42 AM
 */

namespace app\controllers\admin;


use app\helpers\Paginator;
use app\lib\helpers\ImageResizer;
use app\lib\helpers\Slug;
use app\lib\web\FlashMessages;
use app\models\Category;
use app\models\News;
use Gregwar\Image\Image;

class NewsController extends ControllerBase
{
    public function indexAction()
    {
        $where = '';
        $search = $this->getRequest()->get('search');
        if ($search) {
            $where = " AND title LIKE '%".$search."%' ";
        }
        $total = News::model()->count($where);

        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'admin/news?page='.Paginator::NUM_PLACEHOLDER);

        $models = News::model()->fetchAll($where." ORDER BY id DESC ".$pages->getSql());

        $this->view->render("admin/news/index", ['models' => $models, 'search' => $search, 'pages' => $pages]);
    }

    public function createAction()
    {
        $model = News::model();
        $model->status = 1;
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->only(['title', 'category_id', 'title_en', 'content', 'content_en', 'introduce', 'introduce_en', 'status']);
            $image = $this->getRequest()->getFile('image');
            if ($image) {
                $slug = new Slug();
                $data['slug'] = $slug->createSlug('posts', $data['title']);
                $newName =  $data['slug'].'.'.$image->ext;
                if ($image->save(News::IMAGE_PATH, $newName) !== false) {
                    Image::open(News::IMAGE_PATH.$newName)
                        ->zoomCrop(800, 500, 0xffffff)
                        ->save(News::IMAGE_PATH.$newName);
                    $data['image'] = News::IMAGE_PATH.$newName;
                    $data['type'] = News::TYPE;
                    try {
                        if (($id = $model->save($data))) {
                            FlashMessages::success('Thêm bài viết thành công');
                            $url = $this->getUrlByButton($id, 'create');
                            $this->redirect($url);
                        }
                    } catch (\Exception $e) {
                        var_dump($e);
                    }
                }
            }
        }
        $this->view->render("admin/news/create", ['model' => $model]);
    }

    public function videoAction()
    {
        
        $model = News::model();
        $model->status = 1;
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->only(['title', 'category_id', 'title_en', 'content', 'content_en', 'introduce', 'introduce_en', 'image', 'status']);
            $data['type'] = News::TYPE_VIDEO;
            $data['image'] = $this->getEmberYoutube($data['image']);
            $slug = new Slug();
            $data['slug'] = $slug->createSlug('posts', $data['title']);
            if (($id = $model->save($data))) {
                FlashMessages::success('Thêm bài viết thành công');
                $url = $this->getUrlByButton($id, 'video');
                $this->redirect($url);
            }
        }
        $this->view->render("admin/news/video", ['model' => $model]);
    }

    private function getEmberYoutube($newContent)
    {
        if (preg_match("#(http://www.youtube.com)?/(v/([-|~_0-9A-Za-z]+)|watch\?v\=([-|~_0-9A-Za-z]+)&?.*?)#i", $newContent)) {
            $vidurl = strstr($newContent,"?v=");
            $vidarray = explode("v=",$vidurl);
            $stripParameters = explode("&",$vidarray[1]);
            $stripBreaks = explode("<br />",$stripParameters[0]);
            $stripSpaces = explode(" ",$stripBreaks[0]);
            return 'http://www.youtube.com/embed/' . $stripSpaces[0];
        }
        return $newContent;
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = News::model()->fetchOne($id);

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->only(['title', 'category_id', 'title_en', 'content', 'content_en', 'introduce', 'introduce_en', 'status']);

            $image = $this->getRequest()->getFile('image');
            $slug = new Slug();
            $data['slug'] = $slug->updateSlug('posts', $data['title'], $model->id);
            if ($image) {
                $newName = $model->slug.'.'.$image->ext;;
                if ($image->save(News::IMAGE_PATH, $newName) !== false) {
                    Image::open(News::IMAGE_PATH.$newName)
                        ->zoomCrop(800, 500, 0xffffff)
                        ->save(News::IMAGE_PATH.$newName);
                    $data['image'] = News::IMAGE_PATH.$newName;
                }
            }


            if (($id = $model->save($data))) {
                FlashMessages::success('Thêm bài viết thành công');
                $url = $this->getUrlByButton($model->id, 'news');
                $this->redirect($url);
            }
        }
        $this->view->render("admin/news/create", ['model' => $model]);
    }

    public function updateVideoAction()
    {
        $id = $this->getRequest()->get('id');
        $model = News::model()->fetchOne($id);
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->only(['title', 'category_id', 'title_en', 'content', 'content_en', 'introduce', 'introduce_en', 'image', 'hot', 'status']);
            $data['image'] = $this->getEmberYoutube($data['image']);
            if (isset($_POST['hot'])) {
                $db = News::model()->getDb();
                $db->prepare('update posts set hot = 0')->execute();
            }
            $slug = new Slug();
            $data['slug'] = $slug->updateSlug('posts', $data['title'], $model->id);
            if ($model->save($data)) {
                FlashMessages::success('Cập nhật bài viết thành công');
                $url = $this->getUrlByButton($model->id, 'video');
                $this->redirect($url);
            }
        }
        $this->view->render("admin/news/video", ['model' => $model]);
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        News::model()->delete($id);
        $this->redirect('admin/news');
    }

    private function getUrlByButton($id, $type)
    {
        if (isset($_POST['save'])) {
            return "admin/news/update-{$type}?id={$id}";
        } elseif (isset($_POST['save-close'])) {
            return 'admin/news';
        } elseif (isset($_POST['save-new'])) {
            return 'admin/news/create-'.$type;
        }
    }
}
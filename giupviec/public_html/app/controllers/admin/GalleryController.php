<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 1/10/2017
 * Time: 4:38 PM
 */

namespace app\controllers\admin;


use app\helpers\Paginator;
use app\lib\helpers\Slug;
use app\lib\web\FlashMessages;
use app\models\Album;
use app\models\Gallery;
use Gregwar\Image\Image;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class GalleryController extends ControllerBase
{
    public function indexAction()
    {
        $total = Gallery::model()->count();

        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'admin/gallery/images?page=' . Paginator::NUM_PLACEHOLDER);
        $pages->setItemsPerPage(9);
        $type = $this->getRequest()->get('type');
        $models = Album::model()->fetchAll(" WHERE type = {$type} ORDER BY id DESC " . $pages->getSql());
        $this->view->render('admin/gallery/images', ['models' => $models, 'pages' => $pages, 'type' => $type]);
    }

    public function createAction()
    {
        $model = new Album();
        $model->status = 1;
        $type = $this->getRequest()->get('type');
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['name', 'name_en', 'introduce', 'introduce_en', 'status']);
            $slug = new Slug();
            $data['slug'] = $slug->createSlug('albums', $data['name']);
            $data['status'] = $this->getRequest()->getParam('status', 0);
            $data['type'] = $type;
            if (!empty($data['slug'])) {
                $path = ROOT_PATH . '/images/gallery/' . $data['slug'];
                if (!is_dir($path)) {
                    mkdir($path, 0777);
                }
                $image = $this->getRequest()->getFile('image');
                if ($image->error === UPLOAD_ERR_OK) {
                    $newName = $data['slug'] . '.' . $image->ext;
                    if ($image->save(Album::IMAGE_PATH, $newName) !== false) {
                        Image::open(Album::IMAGE_PATH . $newName)
                            ->zoomCrop(340, 210, 0xffffff)
                            ->save(Album::IMAGE_PATH . $newName);
                        $data['image'] = Album::IMAGE_PATH . $newName;
                    }
                    $id = $model->save($data);
                    FlashMessages::success('Thêm album thành công.');
                    $this->redirect('admin/gallery/update?id=' . $id);
                }
                FlashMessages::error('Bạn chưa chọn hình ảnh đại diện.');
                $model->map($data);
            }
        }
        $this->view->render("admin/gallery/create", ['model' => $model, 'type' => $type]);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->get('id');
        $model = Album::model()->fetchOne($id);
        if ($this->getRequest()->isPost()) {
            $data = $this->_request->only(['name', 'name_en', 'introduce', 'introduce_en', 'status']);
            $image = $this->getRequest()->getFile('image');

            if ($image) {
                $newName = $model->slug . '.' . $image->ext;
                if ($image->save(Album::IMAGE_PATH, $newName) !== false) {
                    Image::open(Album::IMAGE_PATH . $newName)
                        ->zoomCrop(340, 210, 0xffffff)
                        ->save(Album::IMAGE_PATH . $newName);
                    $data['image'] = Album::IMAGE_PATH . $newName;
                }
            }
            $data['status'] = $this->getRequest()->getParam('status', 0);
            $model->save($data);
            $src = $this->getRequest()->getParam('src');
            if (isset($_POST['add']) && $src) {
                $video = new Gallery();
                $video->save(['src'=>$this->getEmberYoutube($src), 'album_id' => $model->id]);
                FlashMessages::success('Thêm video thành công.');
                $this->redirect('admin/gallery/update?id='.$model->id);
            }
            FlashMessages::success('Thêm album thành công.');
            $this->redirect('admin/gallery?type=' . $model->type);
        }
        $this->view->render("admin/gallery/create", ['model' => $model, 'type' => $model->type]);
    }

    public function delAction()
    {
        $id = $this->getRequest()->get('id');
        $album = Album::model()->fetchOne($id);
        Album::model()->delete($id);
        $path = ROOT_PATH . '/images/gallery/' . $album->slug;
        try {
            $this->rmdir_recursive($path);
            FlashMessages::success('Xóa album thành công.');
        } catch (\Exception $e) {

        }
        $this->redirect('admin/gallery?type='.$album->type);
    }

    private function rmdir_recursive($dir)
    {
        $it = new RecursiveDirectoryIterator($dir);
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            if ('.' === $file->getBasename() || '..' === $file->getBasename()) continue;
            if ($file->isDir()) rmdir($file->getPathname());
            else unlink($file->getPathname());
        }
        rmdir($dir);
    }


    public function videoAction()
    {
        if ($this->getRequest()->isPost()) {
            $src = $this->getRequest()->getParam('src');
            $data['created_at'] = time();
            $data['src'] = $this->getEmberYoutube($src);
            if (Gallery::model()->save($data)) {
                FlashMessages::success('Thêm Video thành công.');
                $this->redirect('admin/gallery-video');
            }
        }

        $total = Gallery::model()->count();

        $pages = new Paginator($total, $this->getRequest()->get('page', 1), 'admin/news?page=' . Paginator::NUM_PLACEHOLDER);
        $pages->setItemsPerPage(9);

        $models = Gallery::model()->fetchAll(" ORDER BY id DESC " . $pages->getSql());
        $this->view->render('admin/gallery/video', ['models' => $models, 'pages' => $pages]);
    }

    private function getEmberYoutube($newContent)
    {
        if (preg_match("#(http://www.youtube.com)?/(v/([-|~_0-9A-Za-z]+)|watch\?v\=([-|~_0-9A-Za-z]+)&?.*?)#i", $newContent)) {
            $vidurl = strstr($newContent, "?v=");
            $vidarray = explode("v=", $vidurl);
            $stripParameters = explode("&", $vidarray[1]);
            $stripBreaks = explode("<br />", $stripParameters[0]);
            $stripSpaces = explode(" ", $stripBreaks[0]);
            return 'http://www.youtube.com/embed/' . $stripSpaces[0];
        }
        return $newContent;
    }


    public function deleteAction()
    {
        $id = $this->getRequest()->get('id');
        $gallery = Gallery::model()->fetchOne($id);
        Gallery::model()->delete($id);
        $this->redirect('admin/gallery/update?id='.$gallery->album_id);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Phan
 * Date: 12/27/2016
 * Time: 9:44 AM
 */

namespace app\controllers;

use app\lib\auth\AuthComponent;
use app\lib\base\Controller;
use app\lib\base\Request;
use app\lib\web\Language;
use app\models\Setting;
use app\models\User;

class ControllerBase extends Controller
{
    /**
     * @var User
     */
    public $user;

    public $auth;



    /**
     * @var Language
     */
    public $lang;

    public function init()
    {
        parent::init();
        $this->lang = new Language();
        $lang = $this->router->getSegment(1);
        $langs = $this->lang->getLanguageList();

        if (in_array($lang, $langs)) {
            $_SESSION['lang'] = $lang;

            if ($lang == $this->lang->getDefault()) {
                $this->redirect($this->router->_getUri());
            }
        } elseif (!isset($_SESSION['lang'])) {
            $_SESSION['lang'] =  $this->lang->getDefault();
        }
        $this->lang->load('home');

        $this->auth = new AuthComponent();
        $this->user = $this->auth->getUser();

        $tmpSettings = Setting::model()->fetchAll('', true);
        $settings = [];
        foreach ($tmpSettings as $setting) {
            $settings[$setting['name']] = $setting['value'];
        }
        $this->view->settings = $settings;
    }
}
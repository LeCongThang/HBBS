<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 10:06 AM
 */

namespace app\lib\auth;


use app\lib\base\Component;
use app\lib\base\Database;
use App\Lib\Base\Request;
use app\lib\web\Session;
use app\models\User;

class AuthComponent
{
    public $_identity;

    protected $cookieName = 'token';

    /**
     * Login
     * @param $username
     * @param $password
     * @param $remember
     * @return bool
     */
    public function auth($username, $password, $remember)
    {
        if ($this->isUser()) {
            return true;
        }

        $user = $this->check($username, $password);

        if ($user != null) {
            $this->_identity = $user;
            Session::set("user_id", $user->id);
            //Session::set("role", $user->role);
            if ($remember) {
                $this->createCookie($user);
            }

            return true;
        }

        return false;
    }

    /**
     * Check user login
     * @return bool
     */
    public function isUser()
    {
        return $this->_identity != null;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        if ($this->isUser()) {

        } else {
            $id = Session::getUserId();
            if ($id) {
                $this->_identity = User::model()->fetchOne($id);
            } elseif (isset($_COOKIE[$this->cookieName])) {

                $this->_identity = $this->checkByAuthKey($_COOKIE[$this->cookieName]);
            }
        }
        return $this->_identity;
    }

    /**
     * Do login
     * @return User|bool
     * @param $username
     * @param $password
     */
    protected function check($username, $password)
    {
        $db = Database::openConnection();
        $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $db->execute([$username]);
        $user = $db->getStatement()->fetchObject(User::class);

        if ($user && $this->validatePassword($password, $user->password)) {
            return $user;
        }
        return null;
    }

    /**
     * Login by token
     * @param $key
     * @return mixed
     */
    public function checkByAuthKey($key)
    {
        $db = Database::openConnection();
        $db->prepare("SELECT * FROM users WHERE auth_key = ? LIMIT 1");
        $db->execute([$key]);
        $user = $db->getStatement()->fetchObject(User::class);
        return $user;
    }

    /**
     * Verifies a password against a hash
     * @param $password
     * @param $hash
     * @return bool
     */
    public function validatePassword($password, $hash)
    {
        if (function_exists('password_verify')) {
            return password_verify($password, $hash);
        }

        return false;
    }

    /**
     * Set cookie user logged
     * @param User $user
     */
    private function createCookie(User $user)
    {
        $auth_key = hash('sha256', $user->id . $user->role);
        setcookie($this->cookieName, $auth_key, time() + (86400 * 30), "/");
        $user->save(['auth_key' => $auth_key]);
    }

    /**
     *  Logout session
     */
    public function logout()
    {
        Session::getAndDestroy("user_id");
        unset($_COOKIE[$this->cookieName]);
        setcookie($this->cookieName, null, -1, '/');
    }
}
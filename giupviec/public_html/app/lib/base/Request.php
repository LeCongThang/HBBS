<?php
namespace app\lib\base;

use app\lib\helpers\FileUpload;
use App\Lib\Web\Language;

class Request
{
    protected $_files;

    public function __construct()
    {
        $this->_files = $this->reArrayFiles();
    }


    /**
     * Tests if the current request is a POST request
     * @return boolean
     */
    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST' ? true : false);
    }

    /**
     * Tests if the current request is a GET request
     * @return boolean
     */
    protected function _isGet()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'GET' ? true : false);
    }

    /**
     * fetches the given parameter data.
     * @param string $key the key to look for.
     * @param mixed $default the default value to return, if the given parameter is not set.
     */
    public function getParam($key, $default = null)
    {
        if ($this->isPost()) {
            if (isset($_POST[$key])) {
                return $_POST[$key];
            }
        } else if ($this->_isGet()) {
            if (isset($_GET[$key])) {
                return $_GET[$key];
            }
        }

        return $default;
    }

    public function only($allowed = [])
    {
        $filtered = array_filter(
            $_POST,
            function ($key) use ($allowed) {
                return in_array($key, $allowed);
            },
            ARRAY_FILTER_USE_KEY
        );

        return $filtered;
    }

    public function get($key, $default = '')
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        return $default;
    }

    /**
     * Returns a list of parameters given in the current request
     * @return array the params given
     */
    public function getAllParams()
    {
        if ($this->isPost()) {
            return $_POST;
        } else if ($this->_isGet()) {
            return $_GET;
        }
    }

    private function reArrayFiles()
    {
        $files = [];

        foreach ($_FILES as $key => $file) {
            $file_ary = array();
            $file_count = count($file['name']);
            $file_keys = array_keys($file);
           if ($file_count > 1) {
               for ($i = 0; $i < $file_count; $i++) {
                   $arrTmp = [];
                   foreach ($file_keys as $key) {
                       $arrTmp[$key] = $file[$key][$i];
                   }
                   $file_ary[$i] = new FileUpload($arrTmp);
               }
           } else {
               $file_ary = new FileUpload($file);
           }


            $files[$key] = $file_ary;
        }

        return $files;
    }

    /**
     * Get All file upload
     * @return array
     */
    public function getFiles()
    {
        return $this->_files;
    }

    /**
     * Get file upload by name
     * @return FileUpload
     */
    public function getFile($name, $default = null)
    {
        if (isset($this->_files[$name])) {
            return $this->_files[$name];
        }
        return $default;
    }

}

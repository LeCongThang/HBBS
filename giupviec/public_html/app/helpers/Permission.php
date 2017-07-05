<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/22/2016
 * Time: 2:35 PM
 */

namespace app\helpers;


use app\lib\base\Component;

class Permission
{
    public $permissions;

    protected $roles = [];

    protected $itemsTemplate = "<ul>{items}</ul>";
    protected $itemTemplate = "<li><label for=''><input type='checkbox' name='permissions[]' value='{val}' {checked}> {name}</label>{items}</li>";

    /**
     * Permission constructor.
     */
    public function __construct()
    {
        $this->permissions = include(ROOT_PATH . 'app/config/permissions.php');
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param $items
     * @return string
     */
    public function renderItems($items)
    {
        $result = '';
        foreach ($items as $key => $item) {
            $result .= $this->renderItem($key, $item);
        }
        return strtr($this->itemsTemplate, ['{items}' => $result]);
    }

    /**
     * @param $key
     * @param $item
     * @return string
     */
    private function renderItem($key, $item)
    {
        $items = '';
        if (isset($item['items'])) {
            $items = $this->renderItems($item['items']);
        }
        if (is_array($item)) {
            $_item = reset($item);
            $key = key($item);;
        } else {
            $_item = $item;
        }

        $checked = '';
        if (in_array($key , $this->roles)) {
            $checked = 'checked';
        }

        return strtr($this->itemTemplate, ['{val}' => $key, '{name}' => $_item, '{checked}' => $checked, '{items}' => $items]);
    }

    /**
     * Render list permission checkbox
     * @return mixed
     */
    public static function renderView($roles)
    {
        $permission = new Permission();
        $permission->setRoles($roles);
        return $permission->renderItems($permission->permissions);
    }

}
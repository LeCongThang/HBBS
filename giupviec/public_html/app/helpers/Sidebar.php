<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 11:12 AM
 */

namespace app\helpers;


use app\models\User;

class Sidebar
{
    public $items = [];
    public $currentUrl;

    protected $itemTemplate = "<li class='{class}'>
                <a href='{url}'>
                    <i class='glyphicon {icon}'></i> <span>{title}</span>
                    {dropdown}
                </a>
                {items}
            </li>";
    protected $itemNolink = "<li class=\"header\">{title} {items}</li>";

    protected $listTemplate = "<ul class='{class}'>{items}</ul>";

    public $role;

    function __construct($items, $role)
    {
        $this->items = $items;
        $this->role = $role;
        $this->currentUrl = $this->_getUri();

    }

    /**
     * Fetches the current URI called
     * @return string the URI called
     */
    protected function _getUri()
    {
        $uri = explode('?', $_SERVER['REQUEST_URI']);
        $uri = $uri[0];
        $uri = substr($uri, strlen(WEB_ROOT));
        return $uri;
    }

    public static function run($items, $role)
    {
        $menu = new Sidebar($items, $role);
        return $menu->render();
    }

    public function render()
    {
        return $this->renderItems($this->items);
    }

    protected function renderItems($item)
    {
        if (!isset($item['items'])) {
            return '';
        }

        $items = $item['items'];
        $result = "";
        foreach ($items as $_item) {
            $result .= $this->renderItem($_item);
        }

        $replace = ['{items}' => $result];

        if (isset($item['class'])) {
            $replace['{class}'] = $item['class'];
        } else {
            $replace['{class}'] = 'treeview-menu';
        }

        return strtr($this->listTemplate, $replace);
    }

    protected function renderItem($item)
    {
        if ($this->role == User::USER && !isset($item['allow-user'])) {
            return '';
        }
        if (!isset($item['url'])) {
            $replace = ['{title}' => $item['title']];
            $template = $this->itemNolink;
        } else {
            $replace = [
                '{title}' => $item['title'],
                '{icon}' => isset($item['icon']) ? $item['icon'] : '',
                '{class}' => isset($item['class']) ? $item['class'] : '',
                '{url}' => BASE_URL . $item['url'],
            ];
            if ($this->currentUrl == '/' . $item['url']) {
                $replace['{class}'] .= ' active';
            }
            $template = $this->itemTemplate;
        }

        if (isset($item['items'])) {
            $replace['{class}'] .= ' treeview';
            $replace['{items}'] = $this->renderItems($item);
            $replace['{dropdown}'] = "<span class=\"pull-right-container\">
              <i class=\"fa fa-angle-left pull-right\"></i>
            </span>";
        } else {
            $replace['{dropdown}'] = '';
            $replace['{items}'] = '';
        }

        return strtr($template, $replace);
    }
}
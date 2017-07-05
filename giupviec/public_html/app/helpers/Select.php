<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/23/2016
 * Time: 9:16 AM
 */

namespace App\Helpers;


use app\lib\base\Widget;

class Select extends Widget
{
    public $items;

    public $selected = null;

    public $name = '';

    public $selectTemplate = "<select class='{class}' id='{id}' name='{name}'>{options}</select>";

    public $optionTemplate = "<option value='{value}' {selected}>{name}</option>";
    public $class;
    public $id;

    public function __toString()
    {
        return $this->renderItems();
    }


    /**
     * @return string
     */
    private function renderItems()
    {
        $result = '';
        $items = $this->items;
        foreach ($items as $key => $item) {
            $result .= $this->renderItem($key, $item);
        }
        $data['{options}'] = $result;
        if ($this->class) {
            $data['{class}'] = $this->class;
        }

        $data['{id}'] = $this->id?$this->id:'';

        $data['{name}'] = $this->name;

        return strtr($this->selectTemplate, $data);
    }

    /**
     * @param $key
     * @param $item
     * @return string
     */
    private function renderItem($key, $item)
    {
        $data['{value}'] = $key;
        $data['{name}'] = $item;
        $data['{selected}'] = '';
        if ($this->selected == $key) {
            $data['{selected}'] = 'selected';
        }

        return strtr($this->optionTemplate, $data);
    }
}
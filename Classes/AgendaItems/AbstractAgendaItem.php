<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2018 Christoph Fischer, http://www.peregrinus.de
 * Author: Christoph Fischer, chris@toph.de
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Peregrinus\Pulpit\AgendaItems;

use Peregrinus\Pulpit\Debugger;

class AbstractAgendaItem
{

    protected $title = '';
    protected $_hasFields = true;
    public $buttonStyle = 'fa fa-plus-circle';

    public function __construct()
    {
    }

    /**
     * Get the key for this AgendaItem
     * @return string
     */
    public function getKey()
    {
        $tmp = explode('\\', get_class($this));
        return lcfirst(str_replace('AgendaItem', '', array_pop($tmp)));
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function renderDataForm($id, $name, $value)
    {
        $changeFunc = 'var text = $(this).val(); if (text != \'\') text = text.substr(0,80)+\'...\'; $(\'#\'+$(this).parent().data(\'preview\')).html(text);';

        $o = '<textarea style="width:100%" id="' . $id . '" name="' . $name . '" onchange="'.$changeFunc.'"  onkeyup="'.$changeFunc.'">' . $value . '</textarea>';
        return $o;
    }


    public function hasFields(): bool {
        return $this->_hasFields;
    }

    public function provideData($data) {
        return $data;
    }

    public function renderTitle($title) {
        return '<span class="pulpit-detailed-liturgy-form-single-toggle">' .$title.'</span>';
    }

    public function renderDataPreview($data) {
        return print_r($data, 1);
    }

    public function renderToolBarButton($key) {
        return '<a class="button button-small pulpit-detailed-liturgy-field-btn-add-item" href="#" data-type="'
            . $this->getKey() . '" data-key="'.$key.'" title="'
            . sprintf(__('Add %s', 'pulpit'), $this->getTitle()).'">'
            .'<span class="'.$this->buttonStyle.'"></span>'
            . '</a>&nbsp;';

    }

}

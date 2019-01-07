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

class ScriptureReadingAgendaItem extends AbstractAgendaItem
{
    protected $buttonStyle = 'fa fa-bible';

    public function __construct()
    {
        parent::__construct();
        $this->setTitle(__('Scripture reading', 'pulpit'));
    }

    public function renderDataForm($id, $name, $value)
    {
        if (!is_array($value)) $value = ['reference' => ''];

        $changeFunc = '$(\'#\'+$(this).parent().data(\'preview\')).html($(this).val());';

        return '<label for="'.$name.'[reference]">'.__('Reference', 'pulpit').'</label>'
            .'<input type="text" style="width: 100%" id="'.$id.'_reference" name="'.$name.'[reference]" value="'
            .($value['reference'] ?: '')
            .'" onchange="'.$changeFunc.'"  onkeyup="'.$changeFunc.'"/>';
    }

    public function renderDataPreview($data)
    {
        return $data['reference'];
    }

}

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
use Peregrinus\Pulpit\Service\SongService;

class SongAgendaItem extends AbstractAgendaItem
{

    public $buttonStyle = 'fa fa-music';

    public function __construct()
    {
        parent::__construct();
        $this->setTitle(__('Song', 'pulpit'));
    }

    public function renderDataForm($id, $name, $value)
    {
        if (!is_array($value)) {
            $value = ['song' => $value, 'verses' => ''];
        }

        $changeFunc2 = '$(\'#\'+$(this).parent().data(\'preview\')+\' .data-preview-verses\').html($(this).val() ? \'(\' + $(this).val() + \')\' : \'\');';

        return SongService::getInstance()->selectBox($id . '_name', $name . '[song]', $value['song'] ?: '')
            . '<label for="' . $name . '[verses]">' . __('Verses', 'pulpit') . '</label>'
            . '<input type="text" style="width: 100%" id="' . $id . '_verses" name="' . $name . '[verses]" value="'
            . ($value['verses'] ?: '')
            . '" onchange="' . $changeFunc2 . '" onkeyup="' . $changeFunc2 . '" onclick="' . $changeFunc2 . '"/>';
    }

    public function provideData($data)
    {
        return ['song' => SongService::getInstance()->get($data['song']), 'verses' => $data['verses']];
    }

    public function renderDataPreview($data)
    {
        return '<span class="data-preview-song">'
            . SongService::getInstance()->renderSinglePreview($data['song'])
            . '</span> '
            . '<span class="data-preview-verses">'
            . ($data['verses'] ? '('.$data['verses'].')' : '')
            . '</span>';
    }

}

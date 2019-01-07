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

use Peregrinus\Pulpit\Service\EGService;

class PsalmAgendaItem extends AbstractAgendaItem
{
    protected $buttonStyle = 'fa fa-praying-hands';

    public function __construct()
    {
        parent::__construct();
        $this->setTitle(__('Psalm', 'pulpit'));
    }

    public function renderDataForm($id, $name, $value)
    {
        if (!is_array($value)) $value = ['song' => $value];

        $changeFunc2 = '$(\'#\'+$(this).parent().data(\'preview\')+\' .data-preview-verses\').html($(this).val());';

        return EGService::getInstance()->selectBox(
            $id.'_name',
            $name.'[song]',
            $value['song'] ?: '',
            $changeFunc2
        );
    }

    public function provideData($data)
    {
        $song = EGService::getInstance()->get($data['song']);
        $data['number'] = $data['song'];
        $data['isEG'] = is_numeric($data['song']);
        $data['title'] = $song['title'];
        $data['fulltext'] = $song['verses'];
        return $data;
    }

    public function renderDataPreview($data)
    {

        return '<span class="data-preview-song">'
            . EGService::getInstance()->renderSinglePreview($data['song'])
            . '</span>';
    }


}

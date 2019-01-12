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

class TextBlockAgendaItem extends AbstractAgendaItem
{

    public $buttonStyle = 'fa fa-file-alt';

    protected $blocks = [];

    public function __construct()
    {
        parent::__construct();
        $this->setTitle(__('Text block', 'pulpit'));

        // get all text blocks
        $this->blocks = get_posts([
            'post_type' => 'pulpit_textblock',
            'posts_per_page' => -1,
            'order_by' => 'post_title',
            'order' => 'DESC'
        ]);

    }

    public function renderDataForm($id, $name, $value)
    {
        $changeFunc2 = '$(\'#\'+$(this).parent().data(\'preview\')+\' '
            .'.data-preview-block\').html($(this).find(\'option[value=\\\'\'+$(this).val()+\'\\\']\').first().text());';

        $o = '<select class="pulpit-detailed-liturgy-extended-select2" style="width: 100%;" id="'.$id.'" name="'.$name
            .'" onchange="' . $changeFunc2 . '" onkeyup="' . $changeFunc2 . '" onclick="' . $changeFunc2 . '">';

        /** @var \WP_Post $block */
        foreach ($this->blocks as $block) {
            $o .= '<option value="'.$block->ID.'" '.($value == $block->ID ? 'selected' : '').'>'.$block->post_title.'</option>';
        }
        $o .= '</select>';

        return $o;
    }

    public function provideData($data)
    {
        return get_post($data)->post_content;
    }

    public function renderDataPreview($data)
    {
        return '<span class="data-preview-block">'. get_post($data)->post_title. '</span>';
    }

}

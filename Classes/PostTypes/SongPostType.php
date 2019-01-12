<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2017 Christoph Fischer, http://www.peregrinus.de
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

namespace Peregrinus\Pulpit\PostTypes;

use Peregrinus\Pulpit\Admin\MetaBox;
use Peregrinus\Pulpit\Domain\Model\EventModel;
use Peregrinus\Pulpit\Domain\Model\SermonModel;
use Peregrinus\Pulpit\Fields\CheckBoxField;
use Peregrinus\Pulpit\Fields\EventsRelationField;
use Peregrinus\Pulpit\Fields\FileRelationField;
use Peregrinus\Pulpit\Fields\HandoutFormatField;
use Peregrinus\Pulpit\Fields\InputField;
use Peregrinus\Pulpit\Fields\RTEField;
use Peregrinus\Pulpit\Fields\SlideRelationField;
use Peregrinus\Pulpit\Fields\TextAreaField;

/**
 * Class SermonPostType
 * Custom PostType for a sermon
 * @package Peregrinus\Pulpit\PostTypes
 */
class SongPostType extends AbstractPostType
{

    public function __construct()
    {
        $this->labels = [
            'name' => __('Songs', 'pulpit'),
            'singular_name' => __('Song', 'pulpit'),
            'add_new' => _x('Add New', 'pulpit_song', 'pulpit'),
            'add_new_item' => __('Add New Song', 'pulpit'),
            'edit_item' => __('Edit Song', 'pulpit'),
            'new_item' => __('New Song', 'pulpit'),
            'view_item' => __('View Song', 'pulpit'),
            'search_items' => __('Search Songs', 'pulpit'),
            'not_found' => __('No songs found', 'pulpit'),
            'not_found_in_trash' => __('No songs found in Trash', 'pulpit'),
            'menu_name' => __('Songs', 'pulpit'),
        ];

        $this->configuration = [
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => true,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => true,
            'supports' => ['title', 'editor', 'comments', 'thumbnail', 'entry-views'],
        ];

        parent::__construct();

    }

    public function addCustomFields()
    {
        return [
            new MetaBox('general', __('General', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new InputField('number', __('Song number', 'pulpit')),
                    new TextAreaField('copyrights', __('Copyrights', 'pulpit')),
                ]
            ),
        ];
    }
}

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
use Peregrinus\Pulpit\Fields\DetailedLiturgyField;

/**
 * Class SermonPostType
 * Custom PostType for a sermon
 * @package Peregrinus\Pulpit\PostTypes
 */
class AgendaPostType extends AbstractPostType
{

    public function __construct()
    {
        $this->labels = [
            'name' => __('Agendas', 'pulpit'),
            'singular_name' => __('Agenda', 'pulpit'),
            'add_new' => _x('Add New', 'pulpit_location', 'pulpit'),
            'add_new_item' => __('Add New Agenda', 'pulpit'),
            'edit_item' => __('Edit Agenda', 'pulpit'),
            'new_item' => __('New Agenda', 'pulpit'),
            'view_item' => __('View Agenda', 'pulpit'),
            'search_items' => __('Search locations', 'pulpit'),
            'not_found' => __('No locations found', 'pulpit'),
            'not_found_in_trash' => __('No locations found in Trash', 'pulpit'),
            'menu_name' => __('Locations', 'pulpit'),
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


    /**
     * Add the custom fields for this post type
     */
    public function addCustomFields()
    {
        return [
            new MetaBox('liturgy', __('Liturgy', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new MetaBox('liturgy', __('Liturgy', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                            new DetailedLiturgyField('liturgy', [
                                'public_info' => __('This information may be published (e.g. in handouts)'),
                                'responsible' => __('Responsible for this item'),
                                'instructions_for' => __('Instructions for %s'),
                            ]),
                        ]
                    ),
                ]
            ),
        ];
    }


}

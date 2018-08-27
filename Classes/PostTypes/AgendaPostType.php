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
use Peregrinus\Pulpit\Fields\AgendaItemsField;

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

        add_action('pre_get_posts', [$this, 'customSortOrder']);
    }

    /**
     * Register custom columns
     */
    public function registerCustomColumns()
    {
        add_action("manage_posts_custom_column", [$this, 'getCustomColumns']);
        add_filter("manage_edit-portfolio_columns", [$this, 'renderCustomColumn']);
    }

    /**
     * Custom columns for admin view
     * @return array Columns
     */
    public function getCustomColumns()
    {
        return [
            'cb' => '<input type="checkbox">',
            'title' => __('Title', 'pulpit'),
        ];
    }

    /**
     * Render a custom column
     * @param string $column Column name
     */
    public function renderCustomColumn($column)
    {
        global $post;
        $custom = get_post_custom();
        switch ($column) {
            case 'title':
                \the_title();
                echo '<br />' . $custom['subtitle'];
                break;
            case 'date':
                \the_date('Y-m-d');
                break;
            case 'series':
                echo \get_the_term_list($post->ID, PEREGRINUS_PULPIT . '_sermon_series', '', ', ', '');
                break;
            case 'preacher':
                echo \get_the_term_list($post->ID, PEREGRINUS_PULPIT . '_sermon_preacher', '', ', ', '');
                break;
        }
    }

    /**
     * Add the custom fields for this post type
     */
    public function addCustomFields()
    {
        return [
            new MetaBox('liturgy', __('Liturgy', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new AgendaItemsField(
                        'agenda_items',
                        [
                            'title' => __('Title', 'pulpit'),
                            'type' => __('Type', 'pulpit'),
                            'description' => __('Description', 'pulpit'),
                            'optional' => __('This item is optional.', 'pulpit'),
                        ])
                ]
            ),
        ];
    }

    /**
     * Custom sort order for get_posts(): newest sermons first
     * @param $query Query
     */
    public function customSortOrder($query)
    {
        if (!is_admin()) {
            return;
        }

        require_once(ABSPATH . 'wp-admin/includes/screen.php');
        $screen = \get_current_screen();
        if ('edit' == $screen->base
            && PEREGRINUS_PULPIT . '_' . $this->getKey() == $screen->post_type
            && !isset($_GET['orderby'])) {
            $query->set('orderby', 'publish_date');
            $query->set('order', 'DESC');
        }
    }

}

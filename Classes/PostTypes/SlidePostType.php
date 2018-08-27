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
use Peregrinus\Pulpit\Fields\CheckBoxField;

/**
 * Class SlidePostType
 * Custom PostType for a sermon slide
 * @package Peregrinus\Pulpit\PostTypes
 */
class SlidePostType extends AbstractPostType
{

    public function __construct()
    {
        $this->labels = [
            'name' => __('Slides', 'pulpit'),
            'singular_name' => __('Slide', 'pulpit'),
            'add_new' => _x('Add New', 'pulpit_slide', 'pulpit'),
            'add_new_item' => __('Add New Slide', 'pulpit'),
            'edit_item' => __('Edit Slide', 'pulpit'),
            'new_item' => __('New Slide', 'pulpit'),
            'view_item' => __('View Slide', 'pulpit'),
            'search_items' => __('Search Slides', 'pulpit'),
            'not_found' => __('No slides found', 'pulpit'),
            'not_found_in_trash' => __('No slides found in Trash', 'pulpit'),
            'menu_name' => __('Slides', 'pulpit'),
        ];

        $this->configuration = [
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => true,
            'has_archive' => true,
            'hierarchical' => true,
            'supports' => ['title', 'editor', 'comments', 'thumbnail', 'entry-views', 'page-attributes'],
        ];

        parent::__construct();

        // register filter for parent type
        add_filter('page_attributes_dropdown_pages_args', [$this, 'registerChangedParentType'], 10, 2);
    }

    /**
     * Change the post_type of the posts listed in the parent dropdown
     * This needs to be done, because the parent of a slide should be a sermon, not another slide
     */
    public function registerChangedParentType(array $dropdownConfiguration, \WP_Post $post)
    {
        if ($post->post_type == $this->getTypeName()) {
            $dropdownConfiguration['post_type'] = 'pulpit_sermon';
            $dropdownConfiguration['show_option_none'] = __('No sermon selected.', 'pulpit');
        }
        return $dropdownConfiguration;
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
        }
    }

    /**
     * Add the custom fields for this post type
     */
    public function addCustomFields()
    {
        return [
            new MetaBox('slide_options', __('Slide options', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new CheckBoxField('suppress_title', __('Suppress title on exported slide', 'pulpit')),
                ]
            ),
        ];
    }

}

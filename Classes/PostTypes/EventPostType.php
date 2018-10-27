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
use Peregrinus\Pulpit\Fields\DateInputField;
use Peregrinus\Pulpit\Fields\DetailedLiturgyField;
use Peregrinus\Pulpit\Fields\InputField;
use Peregrinus\Pulpit\Fields\LocationRelationField;
use Peregrinus\Pulpit\Fields\RTEField;

/**
 * Class SermonPostType
 * Custom PostType for a sermon
 * @package Peregrinus\Pulpit\PostTypes
 */
class EventPostType extends AbstractPostType
{

    public function __construct()
    {
        $this->labels = [
            'name' => __('Events', 'pulpit'),
            'singular_name' => __('Event', 'pulpit'),
            'add_new' => _x('Add New', 'pulpit_event', 'pulpit'),
            'add_new_item' => __('Add New Event', 'pulpit'),
            'edit_item' => __('Edit Event', 'pulpit'),
            'new_item' => __('New Event', 'pulpit'),
            'view_item' => __('View Event', 'pulpit'),
            'search_items' => __('Search events', 'pulpit'),
            'not_found' => __('No events found', 'pulpit'),
            'not_found_in_trash' => __('No events found in Trash', 'pulpit'),
            'menu_name' => __('Events', 'pulpit'),
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

        add_filter('wp_title', [$this, 'registerTitle']);
        add_filter('get_the_archive_title', [$this, 'registerTitle']);
    }

    /**
     * Register custom columns
     */
    public function registerCustomColumns()
    {
        add_filter('manage_' . $this->getTypeName() . '_posts_columns', [$this, 'getCustomColumns']);
        add_action('manage_' . $this->getTypeName() . '_posts_custom_column', [$this, 'renderCustomColumn'], 10, 2);
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
            'event_date' => __('Date', 'pulpit'),
            'event_time' => __('Time', 'pulpit'),
            'location' => __('Location', 'pulpit'),
        ];
    }

    /**
     * Render a custom column
     * @param string $column Column name
     */
    public function renderCustomColumn($column, $post_id)
    {
        $post = get_post($post_id);
        if ($post->post_type == $this->getTypeName()) {
            $custom = get_post_meta($post->ID);
            switch ($column) {
                case 'title':
                    echo $post->post_title;
                    break;
                case 'event_date':
                    echo strftime(__('%Y-%m-%d', 'pulpit'), strtotime($custom['date'][0] . ' ' . $custom['time'][0]));
                    break;
                case 'event_time':
                    echo strftime(__('%H:%M', 'pulpit'), strtotime($custom['date'][0] . ' ' . $custom['time'][0]));
                    break;
                case 'location':
                    echo get_post($custom['location'][0])->post_title;
                    break;
                case 'short_description':
                    echo $custom['short_description'][0];
                    break;
            }
        }
    }

    /**
     * Add the custom fields for this post type
     */
    public function addCustomFields()
    {
        return [
            new MetaBox('general', __('General', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new DateInputField('date', __('Date', 'pulpit')),
                    new InputField('time', __('Time', 'pulpit')),
                    new LocationRelationField('location', __('Location', 'pulpit')),
                ]
            ),
            new MetaBox('church_calendar', __('Church calendar', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new InputField('day_title', __('Title of the day', 'pulpit')),
                    new RTEField('day_description', __('description', 'pulpit')),
                ]
            ),
            new MetaBox('liturgy', __('Liturgy', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new DetailedLiturgyField('liturgy', [
                        'public_info' => __('This information may be published (e.g. in handouts)'),
                        'responsible' => __('Responsible for this item'),
                        'instructions_for' => __('Instructions for %s'),
                    ]),
                ]
            ),
            new MetaBox('officiating', __('Officiating', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                ]
            ),
            new MetaBox('events_info', __('General information', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new RTEField('published_announcements', __('Announcements for publication', 'pulpit')),
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

    /**
     * Change the title of the archives page
     */
    public function registerTitle($title)
    {
        if (is_post_type_archive($this->getTypeName())) {
            $title = __('Upcoming events', 'pulpit');
        }
        return $title;
    }

}

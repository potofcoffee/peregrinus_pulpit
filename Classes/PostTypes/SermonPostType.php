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
class SermonPostType extends AbstractPostType
{

    public function __construct()
    {
        $this->labels = [
            'name' => __('Sermons', 'pulpit'),
            'singular_name' => __('Sermon', 'pulpit'),
            'add_new' => _x('Add New', 'pulpit_sermon', 'pulpit'),
            'add_new_item' => __('Add New Sermon', 'pulpit'),
            'edit_item' => __('Edit Sermon', 'pulpit'),
            'new_item' => __('New Sermon', 'pulpit'),
            'view_item' => __('View Sermon', 'pulpit'),
            'search_items' => __('Search Sermons', 'pulpit'),
            'not_found' => __('No sermons found', 'pulpit'),
            'not_found_in_trash' => __('No sermons found in Trash', 'pulpit'),
            'menu_name' => __('Sermons', 'pulpit'),
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
            'show_in_rest' => false,
        ];

        parent::__construct();

        add_action('pre_get_posts', [$this, 'customSortOrder']);
    }

    public function addPostTypeSpecificJS()
    {
        parent::addPostTypeSpecificJS();
        wp_enqueue_script('media-upload'); //Provides all the functions needed to upload, validate and give format to files.
        wp_enqueue_script('thickbox'); //Responsible for managing the modal window.
        wp_enqueue_script('pulpit-uploader', PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/Uploader.js', null, null, true);
        wp_enqueue_script('pulpit-speech', PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/Speech.js', null, null, true);
        wp_localize_script(
            'pulpit-speech',
            'pulpit_speech',
            [
                'speech_time' => __('Speaking time', 'pulpit')
            ]
        );
    }

    /**
     * Register custom columns
     */
    public function registerCustomColumns()
    {
        add_action("manage_pulpit_sermon_posts_columns", [$this, 'getCustomColumns']);
        add_filter("manage_pulpit_sermon_posts_custom_column", [$this, 'renderCustomColumn']);
    }

    /**
     * Custom columns for admin view
     * @return array Columns
     */
    public function getCustomColumns($columns)
    {
        return [
            'cb' => '<input type="checkbox">',
            'date' => __('Published'),
            'title' => __('Title', 'pulpit'),
            'subtitle' => __('Subtitle', 'pulpit'),
            'events' => __('Events', 'pulpit'),
            'series' => __('Series', 'pulpit'),
        ];
    }

    /**
     * Render a custom column
     * @param string $column Column name
     */
    public function renderCustomColumn($column)
    {
        global $post;
        $sermon = new SermonModel($post);
        switch ($column) {
            case 'title':
                \the_title();
                echo '<br />' . $meta['subtitle'][0];
                break;
            case 'subtitle':
                echo $sermon->getSubtitle();
                break;
            case 'events':
                $events = [];
                /** @var EventModel $event */
                foreach ($sermon->getEvents() as $event) {
                    $events[] = '<a href="' . get_permalink($event->ID) . '">'
                        . (new \DateTime($event->getDate() . ' ' . $event->getTime()))->format(__('d.m.Y, H:i',
                            'pulpit')) . ' ' . $event->getLocation()->getTitle()
                        . '</a>';
                }
                echo join(', ', $events);
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
            new MetaBox('general', __('General', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new InputField('subtitle', __('Subtitle', 'pulpit')),
                ]
            ),
            new MetaBox('study', __('Study Materials', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new InputField('reference', __('Bible reference', 'pulpit')),
                    new TextAreaField('bible_text', __('Bible text', 'pulpit'), 10),
                    new InputField('notes_header', __('Notes header', 'pulpit')),
                    new TextAreaField('key_points', __('Key points', 'pulpit'), 5),
                    new TextAreaField('questions', __('Small group questions', 'pulpit'), 5),
                    new RTEField('further_reading', __('Further reading', 'pulpit'), 5),
                ]
            ),
            new MetaBox('prep', __('Preparation', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                new TextAreaField('prep', __('How to prepare for the service', 'pulpit'), 5),
            ]),
            new MetaBox('resources', __('Resources', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                new CheckBoxField('cclicense', __('This sermon is released under CC-BY-SA 4.0', 'pulpit')),
                new InputField('handout', __('Handout', 'pulpit')),
                new CheckBoxField('no_handout', __('Don\'t show links to handout', 'pulpit')),
                new HandoutFormatField('handout_format', __('Handout layout', 'pulpit')),
                new InputField('image', __('Title image', 'pulpit')),
                new InputField('preview_image', __('Preview image', 'pulpit')),
                new InputField('image_credits', __('Image credits', 'pulpit')),
                new FileRelationField('audiorecording_relation',
                    __('Audio recording <b>REL!</b>', 'pulpit'),
                    'audio/mpeg',
                    __('Select audio file', 'pulpit'),
                    __('Select audio file', 'pulpit')
                ),
                new InputField('audiorecording', __('Audio recording', 'pulpit')),
                new InputField('remote_audio', __('Remote audio file', 'pulpit')),
                new InputField('videorecording', __('Video recording', 'pulpit')),
            ]),
            new MetaBox('slideshow', __('Slideshow', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                new SlideRelationField('slides',
                    __('Slides', 'pulpit'),
                    'audio/mpeg',
                    __('Select slide', 'pulpit'),
                    __('Select slide', 'pulpit')
                ),
            ]),
            new MetaBox('events', __('Associated events', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new EventsRelationField('events', __('Event', pulpit)),
                ]
            ),
            new MetaBox('ebook', __('E-book', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                new InputField('link_amazon', __('Amazon link', 'pulpit')),
                new InputField('link_smashwords', __('Smashwords link', 'pulpit')),
            ]),
            new MetaBox('remote', __('Synchronization', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
                    new InputField('remote_url', __('Remote URL', 'pulpit')),
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
     * Allow single view for future sermons
     * @param \WP_Query $query
     */
    public function preGetPostsHook(\WP_Query $query)
    {
        if ($query->is_singular() && ($query->query_vars['post_type'] == $this->getTypeName())) {
            $query->set('post_status', ['future', 'publish', 'pulpit_hidden']);
        }
    }

}

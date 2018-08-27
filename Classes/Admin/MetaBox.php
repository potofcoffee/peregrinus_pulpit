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

namespace Peregrinus\Pulpit\Admin;

class MetaBox
{

    protected $key = '';
    protected $label = '';
    protected $fields = [];
    protected $screen = '';
    protected $position = 'normal';
    protected $priority = 'default';

    /**
     * MetaBox constructor.
     *
     * @param string $key Key
     * @param string $label Label
     * @param string|array $screen Screen
     * @param string $position Position
     * @param string $priority Priority
     * @param array $fields Fields
     */
    public function __construct($key, $label, $screen, $position, $priority, $fields)
    {
        $this->key = $key;
        $this->label = $label;
        $this->screen = $screen;
        $this->position = $position;
        $this->priority = $priority;
        $this->fields = $fields;
    }

    /**
     * Register this meta box
     */
    public function register()
    {
        global $wp_meta_boxes;
        \add_meta_box(
            $this->key . '_meta',
            __($this->label, 'pulpit'),
            [$this, 'render'],
            $this->screen,
            $this->position,
            $this->priority
        );
        // allow fields to register more stuff
        foreach ($this->fields as $field) {
            $field->register();
        }
        \add_action('save_post', [$this, 'save']);
    }

    /**
     * Render this meta box
     */
    public function render()
    {
        global $post;

        $customFieldValues = get_post_custom($post->ID);

        foreach ($this->fields as $field) {
            echo $field->render($customFieldValues);
        }
    }

    /**
     * Save the new meta-data
     */
    public function save()
    {
        global $post;
        foreach ($this->fields as $field) {
            $field->save($post->ID);
        }
    }
}

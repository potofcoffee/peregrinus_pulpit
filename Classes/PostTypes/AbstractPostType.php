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

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\FrontendDispatcher;

/**
 * Class AbstractPostType
 * This class contains the basic functionality for all PostTypes and should be extended in order to add individual
 * configuration and functionality.
 * @package Peregrinus\Pulpit\PostTypes
 */
class AbstractPostType
{

    public $labels = [];
    protected $configuration = [];

    /**
     * AbstractPostType constructor.
     */
    public function __construct()
    {
        $this->configuration['labels'] = $this->labels;
        $this->configuration['menu_icon'] = PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Images/PostTypes/' . ucfirst($this->getKey()) . '.svg';
        $this->configuration['slug'] = $this->getSlug();
        $this->configuration['rewrite'] = ['slug' => $this->getSlug(), 'with_front' => false];


        // add some JS to correctly open sidebar menu:
        add_action('add_meta_boxes_'.$this->getTypeName(), [$this, 'addPostTypeSpecificJS']);

        // add pre_get_posts hook
        add_action('pre_get_posts', [$this, 'preGetPostsHook']);
    }

    public function addPostTypeSpecificJS() {
        wp_enqueue_script('pulpit_menu_fix', PEREGRINUS_PULPIT_BASE_URL.'Resources/Public/Scripts/Admin/MenuFix.js');
    }

    /**
     * Template loader
     * Allows overriding WP template loading to provide custom post type templates in this plugin
     * @param $template
     */
    public function templateLoader($template)
    {
        /** @var \WP_Post $queriedObject */
        $queriedObject = get_queried_object();

        foreach ([
                     \WP_Post::class => 'post_type',
                     \WP_Post_Type::class => 'name',
                 ] as $class => $property) {
            if ((is_a($queriedObject, $class)) && ($queriedObject->$property == $this->getTypeName())) {
                $frontend = FrontendDispatcher::getInstance();
                $frontend->setPostType($this->getTypeName());
                $template = $frontend->resolveTemplate($queriedObject, $template);
                continue;
            }
        }

        return $template;
    }

    /**
     * Get the key for this PostType
     * @return string
     */
    public function getKey()
    {
        $tmp = explode('\\', get_class($this));
        return strtolower(str_replace('PostType', '', array_pop($tmp)));
    }

    /**
     * Get the slug for this PostType
     *
     * Normally, the slug will be the key for this PostType, but this can be overridden by
     * setting the slug_<key> option
     *
     * @return mixed
     */
    protected function getSlug()
    {
        return get_option(PEREGRINUS_PULPIT . '_slug_'.$this->getKey(), $this->getKey());
    }

    /**
     * Register custom columns
     */
    public function registerCustomColumns()
    {
    }

    /**
     * Register this PostType
     */
    public function register()
    {
        // register post type
        $res = register_post_type($this->getTypeName(), $this->configuration);

        // register template loader
        add_filter('template_include', [$this, 'templateLoader']);
    }

    /**
     * Get registered type name
     * @param string alternative Optional: get the type name for another key
     * @return string
     */
    public function getTypeName($alternative = '')
    {
        return PEREGRINUS_PULPIT . '_' . ($alternative ?: $this->getKey());
    }

    /**
     * Add all meta boxes / custom fields for this type
     */
    public function addMetaBox()
    {
        foreach ($this->addCustomFields() as $metaBox) {
            $metaBox->register();
        }
    }

    /**
     * Add the custom fields for this post type
     */
    public function addCustomFields()
    {
        return [];
    }

    public function preGetPostsHook($query) {
    }
}

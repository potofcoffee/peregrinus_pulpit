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

namespace Peregrinus\Pulpit\Domain\Model;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Utility\StringUtility;

class AbstractModel
{
    /** @var array Post meta data */
    protected $meta = [];

    /** @var \WP_Post */
    protected $post = null;

    /** @var array  */
    protected $keyMap = [
        '_thumbnail_id' => 'thumbnail'
    ];

    /** @var array $additionalGetters Stores additional getters set by external services */
    protected $additionalGetters = [];

    public function __construct(\WP_Post $post)
    {
        $this->additionalGetters = apply_filters(PEREGRINUS_PULPIT.'_'.$this->getKey().'_getters', []);
        $this->post = $post;
        $meta = get_post_meta($post->ID, null, true);
        foreach ($meta as $key => $val) {
            if (is_array($val) && (count($val) == 1)) {
                $val = $val[0];
            }
            $meta = maybe_unserialize($val);
            $metaMethod = 'set' . StringUtility::UnderscoreToCamelCase($key) . 'Meta';
            if (method_exists($this, $metaMethod)) {
                $this->$metaMethod($meta);
            } else {
                $this->setMetaElement($key, $meta);
            }
        }
        $this->setMetaElement('content', get_extended($this->post->post_content));
    }

    public function getKey()
    {
        $tmp = explode('\\', get_class($this));
        return strtolower(str_replace('Model', '', array_pop($tmp)));
    }

    /**
     * Provide magic get method for meta data and post properties
     * @param $method Property name
     * @return mixed Property value
     */
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }

        if (substr($method, 0, 3) == 'get') {
            $property = StringUtility::CamelCaseToUnderscore(lcfirst(substr($method, 3)));
            if ($property == 'i_d') {
                $property = 'ID';
            }
            if (isset($this->additionalGetters[$property])) {
                // call an additional getter registered through the filter
                array_unshift($args, $this);
                return call_user_func_array($this->additionalGetters[$property], $args);
            } elseif (isset($this->meta[($property)])) {
                return maybe_unserialize($this->meta[$property]);
            } elseif (property_exists($this->post, $property)) {
                return $this->post->$property;
            } elseif (property_exists($this->post, 'post_' . $property)) {
                $property = 'post_' . $property;
                return $this->post->$property;
            }
        }
    }

    public function setMetaElement($key, $meta)
    {
        $key = strtr($key, $this->keyMap);
        $this->meta[$key] = $meta;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    /**
     * @return \WP_Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param \WP_Post $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * @return array
     */
    public function getKeyMap()
    {
        return $this->keyMap;
    }

    /**
     * @param array $keyMap
     */
    public function setKeyMap($keyMap)
    {
        $this->keyMap = $keyMap;
    }



}

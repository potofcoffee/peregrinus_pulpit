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

namespace Peregrinus\Pulpit\Domain\Repository;

use Peregrinus\Pulpit\Debugger;

class AbstractRepository
{

    /** @var array $includepostStatus which posts should be included */
    protected $includepostStatus = ['publish'];

    /**
     * Get a number of records from this repository
     * @param array $args Query data
     * @return array Records
     */
    public function get(array $args = []) {
        $myPostType = PEREGRINUS_PULPIT.'_'.$this->getKey();
        $args['post_type'] = $args['post_type'] ?: $myPostType;
        $args['post_status'] = $args['post_status'] ?: $this->includepostStatus;
        $args['numberposts'] = $args['numberposts'] ?: -1;
        $args['posts_per_page'] = $args['posts_per_page'] ?: -1;

        $rawPosts = get_posts($args);

        $modelObject = 'Peregrinus\\Pulpit\\Domain\\Model\\'.ucfirst($this->getKey()).'Model';
        $output = [];
        foreach ($rawPosts as $post) {
            if (class_exists($modelObject)) {
                $output[] = new $modelObject($post);
            } else {
                $output[] = $post;
            }
        }
        return $output;
    }

    public function findByID($id) {
        $post = get_post($id);
        if ($post) {
            $modelObject = 'Peregrinus\\Pulpit\\Domain\\Model\\'.ucfirst($this->getKey()).'Model';
            if (class_exists($modelObject)) {
                return new $modelObject($post);
            } else {
                return $post;
            }
        }
    }

    /**
     * Get the key for this Repository
     * @return string
     */
    public function getKey()
    {
        $tmp = explode('\\', get_class($this));
        return lcfirst(str_replace('Repository', '', array_pop($tmp)));
    }

    /**
     * @return array
     */
    public function getIncludepostStatus()
    {
        return $this->includepostStatus;
    }

    /**
     * @param array $includepostStatus
     */
    public function setIncludepostStatus($includepostStatus)
    {
        $this->includepostStatus = $includepostStatus;
    }



}

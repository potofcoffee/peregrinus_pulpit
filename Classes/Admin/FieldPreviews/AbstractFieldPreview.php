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


namespace Peregrinus\Pulpit\Admin\FieldPreviews;


class AbstractFieldPreview
{

    /**
     * Render field preview
     * @param \WP_Post $post Post object
     * @param array $meta Meta data
     * @return string
     */
    public static function render(\WP_Post $post, array $meta): string
    {
        return basename($post->guid);
    }

    /**
     * Render filesize in a human readable format
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    protected function filesizeHumanReadable($bytes, $decimals = 0)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor];
    }

}
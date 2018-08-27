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

class AudioFieldPreview extends AbstractFieldPreview
{
    public static function render(\WP_Post $post, $meta): string
    {
        return '<span class="dashicons dashicons-format-audio"></span> '
            . basename($post->guid) . ' (' . $meta['mime_type'] . ', '
            . $meta['length_formatted'] . ', ' . self::filesizeHumanReadable($meta['filesize']) . ')';
    }
}

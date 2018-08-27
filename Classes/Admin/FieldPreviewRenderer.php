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

namespace Peregrinus\Pulpit\Admin;

use Peregrinus\Pulpit\Admin\FieldPreviews\AbstractFieldPreview;

class FieldPreviewRenderer
{

    public static function render($id)
    {
        $post = get_post($id);
        $meta = wp_get_attachment_metadata($id);
        $previewClass = '\\Peregrinus\\Pulpit\\Admin\\FieldPreviews\\'
            . ucfirst(explode('/', $post->post_mime_type)[0]) . 'FieldPreview';
        if (class_exists($previewClass)) {
            /** @var AbstractFieldPreview $previewClass */
            return $previewClass::render($post, $meta);
        } else {
            return AbstractFieldPreview::render($post, $meta);
        }
    }
}

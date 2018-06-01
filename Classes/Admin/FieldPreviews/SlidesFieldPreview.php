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


class SlidesFieldPreview extends AbstractFieldPreview
{
    private static function truncate($text, $length)
    {
        $length = abs((int)$length);
        if (strlen($text) > $length) {
            $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
        }
        return ($text);
    }

    public static function render(\WP_Post $post, $meta): string
    {
        $imgUrl = get_the_post_thumbnail_url($post->ID, 'thumbnail');

        $o .= '<div class="sortable-item pulpit-slide-preview pulpit-clearfix"><div class="pulpit-slide-preview-content">'
            . '<img class="pulpit-slide-preview-image" src="' . $imgUrl . '" float: left;/>'
            .'<div class="pulpit-slide-preview-textcontainer">'
            . '<b>'.$post->post_title.'</b>'
            .'<p>'.self::truncate(strip_tags($post->post_content), 200).'</p>'
            . '</div></div>'
            . '<button class="button button-small open-slider-custom-modal pulpit-slide-delete-button">' . __('Delete', 'pulpit') . '</button>'
            . '<button class="button button-small open-slider-custom-modal pulpit-slide-edit-button">' . __('Edit', 'pulpit') . '</button>'
            . '</div>';
        return $o;
    }


}
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

namespace Peregrinus\Pulpit\Admin\Migrations;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Taxonomies\Song\SongbookTaxonomy;

require_once(ABSPATH . 'wp-admin/includes/image.php');

/**
 * Class EGTextMigration
 * @package Peregrinus\Pulpit\Admin\Migrations
 *
 * This migration does the following:
 * - Import EG song texts from temporary EG.yaml
 */
class EGTextMigration extends AbstractMigration
{
    protected $title = 'EG full text';
    protected $description = 'Add the full text to (most) EG songs';

    protected function generateFeaturedImage($imagePath, $post_id, $songNumber, $songData)
    {
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($imagePath);
        $filename = basename($imagePath);
        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }
        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => $songData['title'],
            'post_content' => 'Noten zu EG'.$songNumber.' "'.$songData['title'].'"',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $file, $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        $res1 = wp_update_attachment_metadata($attach_id, $attach_data);
        $res2 = set_post_thumbnail($post_id, $attach_id);
    }

    public function execute()
    {
        echo '<h1>EGTExtMigration</h1>';
        $tax = new SongbookTaxonomy();


        $psalms = yaml_parse_file(PEREGRINUS_PULPIT_BASE_PATH.'Assets/psalms.yaml');

        foreach ($psalms as $number => $psalm) {

            $content = '<ol>';
            foreach ($psalm['verses'] as $verse) {
                $content .= '<li>'.$verse.'</li>';
            }
            $content .= '</ol>';


            $postId = wp_insert_post([
                'post_type' => 'pulpit_song',
                'post_status' => 'publish',
                'post_title' => $psalm['title'],
                'post_content' => $content,
            ]);

            if (($postId > 0) && (!is_a($postId, 'WP_Error'))) {
                $post = get_post($postId);
                add_post_meta($post->ID, 'number', $number);

                $result = wp_set_post_terms($post->ID, ['Evangelisches Gesangbuch'], $tax->getName());
                if (is_a($result, 'WP_Error')) {
                    /** @var \WP_Error $result */
                    echo $result->get_error_message().'<br />';
                }
                echo '<a href="'.get_the_permalink($post->ID).'" target="blank">'.$post->post_title.'</a><br />';
            }

        }


    }

}

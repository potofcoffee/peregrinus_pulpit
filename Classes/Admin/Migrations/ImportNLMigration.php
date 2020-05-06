<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2020 Christoph Fischer, http://www.peregrinus.de
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

class ImportNLMigration extends AbstractMigration
{

    protected $title = 'NL full text';
    protected $description = 'Add the full text to all NL songs';

    public function execute()
    {
        global $wp_query;

        $songs = yaml_parse_file(WP_PLUGIN_DIR . '/peregrinus-pulpit/Assets/nl.yaml');

        foreach ($songs as $number => $song) {
            echo 'Checking song EG ' . $number . '... ';

            $posts = query_posts([
                'post_type' => 'pulpit_song',
                'tax_query' => [
                    [
                        'taxonomy' => 'pulpit_song_songbook',
                        'field' => 'term_id',
                        'terms' => '488',
                    ]
                ],
                'meta_query' => [
                    [
                        'key' => 'number',
                        'value' => $number,
                        'compare' => '=',
                    ]
                ],
            ]);


            if (count($posts)) {
                $post = $posts[0];
                echo '<span style="color: red">missing</span>';
                $id = wp_update_post([
                    'ID' => $post->ID,
                    'post_type' => 'pulpit_song',
                    'post_title' => $song['title'],
                    'post_author' => 1,
                    'post_status' => 'publish',
                    'post_content' => '<ol><li>'.join('</li><li>', $song['content']).'</li></ol>',
                ]);
                update_post_meta($id, 'copyrights', $song['legal']);
                echo ' --> updated in post #'.$id.'<br />';
            } else {
                echo '<span style="color: red">missing</span><br />';
            }
        }
    }


}

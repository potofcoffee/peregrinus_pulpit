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
use Symfony\Component\Debug\Debug;

class FindEmptySongsMigration extends AbstractMigration
{

    protected $title = 'Empty songs';
    protected $description = 'Find (and delete) empty songs';

    public function execute()
    {
        $posts = query_posts([
            'post_type' => 'pulpit_song',
            'posts_per_page' => -1,
        ]);


        $songs = [];

        foreach ($posts as $song) {
            if (trim(strip_tags($song->post_content)) == '') {
                $meta = get_post_meta($song->ID);
                $terms = wp_get_post_terms($song->ID, 'pulpit_song_songbook');
                $songBook = strtoupper($terms[0]->slug);
                $key = $songBook.' '.$meta['number'][0];
                if (substr($song->post_title, 0, 5 ) == 'Psalm') {
                    wp_delete_post($song->ID, true);
                } else {
                    $songs[$key] = $song;
                }
            }
        }

        ksort($songs);

        foreach ($songs as $key => $song) {
            echo 'Empty: '.$key.' '.$song->post_title.'<br />';
        }
    }


}

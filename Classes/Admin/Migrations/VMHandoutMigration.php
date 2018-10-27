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

/**
 * Class VMHandoutMigration
 * @package Peregrinus\Pulpit\Admin\Migrations
 *
 * This migration does the following:
 * - Find all posts preached at VM FDS (Remote url is something on www.wortzummontag.de
 * - Assign the default VM FDS layout
 * - Clear the remote handout field
 * - Fix key point format (convert obsolete <u></u> to []
 */
class VMHandoutMigration extends AbstractMigration
{
    protected $title = 'VM Handout layout';
    protected $description = 'Set default layout for all VMFDS sermons';

    public function execute()
    {
        echo '<h1>VMHandoutMigration</h1>';

        $rawPosts = get_posts([
            'post_type' => 'pulpit_sermon',
            'posts_per_page'   => -1,
        ]);

        $ctr = 0;

        echo '<ul>';
        $posts = [];
        foreach ($rawPosts as $post) {
            $meta = get_post_meta($post->ID);
            if (strpos($meta['remote_url'][0], '//www.wortzummontag.de/') !== false) {
                update_post_meta($post->ID, 'handout_format', 'VM-FDS');
                update_post_meta($post->ID, 'handout', '');

                if ($keyPoints = $meta['key_points'][0]) {
                    $keyPoints = strtr($keyPoints, ['<u>' => '[', '</u>' => ']']);
                    update_post_meta($post->ID, 'key_points', $keyPoints);
                }

                echo '<li>'.$post->post_title.'</li>';
                $ctr++;
            }
        }
        echo '</ul>';

        echo '<hr />';
        echo $ctr.' sermons updated.';

    }

}

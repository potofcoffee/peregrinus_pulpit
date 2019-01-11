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
 * Class RemoveDescriptiveAgendaItemMigration
 * @package Peregrinus\Pulpit\Admin\Migrations
 *
 * This migration does the following:
 * - Remove all DescriptiveAgendaItems and replace them with FreeTextAgendaItems
 */
class RemoveDescriptiveAgendaItemMigration extends AbstractMigration
{
    protected $title = 'Remove DescriptiveAgendaItems';
    protected $description = 'Replace DescriptiveAgendaItems with FreeTextAgendaItems';

    public function execute()
    {
        echo '<h1>RemoveDescriptiveAgendaItemMigration</h1>';


        // get all events
        $events = get_posts(['post_type' => 'pulpit_event', 'posts_per_page' => -1]);

        /** @var \WP_Post $event */
        foreach ($events as $event) {
            echo $event->ID.': '.$event->post_title.'... ';
            $liturgy = get_post_meta($event->ID, 'liturgy');
            foreach ($liturgy as $key => $item) {
                if ($item['type']=='descriptive') {
                    $item['type'] = 'freeText';
                    $item['data'] = '';
                    $liturgy[$key] = $item;
                }
            }
            update_post_meta($event->ID, 'liturgy', $liturgy);
            echo '<br />';
        }
    }

}

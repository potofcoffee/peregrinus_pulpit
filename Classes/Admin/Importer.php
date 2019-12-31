<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2019 Christoph Fischer, http://www.peregrinus.de
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

use Peregrinus\Pulpit\Debugger;

class Importer
{
    public static function page()
    {
        if (isset($_GET['id'])) {
            return self::import($_GET['id']);
        }

        echo '<table width="100%">';
        $events = json_decode(self::get('https://www.pfarrplaner.de/api/user/1/services'), true);
        foreach ($events['services'] as $event) {
            $title = $event['liturgy']['title'] ? 'Gottesdienst zum ' . $event['liturgy']['title'] : 'Gottesdienst';
            $title = strtr($title, ['n.' => 'nach', 'So.' => 'Sonntag']);
            $date = new \DateTime($event['day']['date']);
            $time = substr($event['time'], 0, 5);

            echo '<tr>';
            echo '<td>';
            if (!($id = self::serviceExists($date, $time))) {
                echo '<a href="' . admin_url('admin.php?page=pulpit_import&id=' . $event['id']) . '" class="button button-small">Importieren</a>';
            } else {
                $post = get_post($id);
                echo edit_post_link('Bereits vorhanden', null, null, $id);
            }
            echo '</td>';
            echo '<td>'.$date->format('d.m.Y').', '.$time.' Uhr</td>';
            echo '<td>'.$event['locationText'].'</td>';
            echo '<td>'.$title.'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }


    public static function import($id)
    {
        echo 'Importiere ' . $id . '<br />';
        $url = 'https://www.pfarrplaner.de/api/service/' . $id;
        echo 'URL: ' . $url . '<br />';

        $event = json_decode(self::get($url), true);

        $title = $event['liturgy']['title'] ? 'Gottesdienst zum ' . $event['liturgy']['title'] : 'Gottesdienst';
        $title = strtr($title, ['n.' => 'nach', 'So.' => 'Sonntag']);
        $date = new \DateTime($event['day']['date']);
        $time = substr($event['time'], 0, 5);

        $locationTitle = $event['locationText'];

        // find location
        $locations = get_posts(['post_type' => PEREGRINUS_PULPIT . '_location', 'posts_per_page' => -1]);
        if (false === ($location = self::findLocation($locationTitle, $locations))) {
            if (isset($event['city']['name'])) $locationTitle .= ' '.$event['city']['name'];
            $location = self::findLocation($locationTitle, $locations);
        }

        $officiating = [];
        foreach (['organists' => 'Organist*in', 'sacristans' => 'Mesner*in'] as $role => $roleTitle) {
            $p = [];
            foreach ($event[$role] as $person) {
                $p[] = $person['name'];
            }
            if (count($p)) $officiating[] = $roleTitle.': '.join(', ', $p);
        }


        $meta = [
            'date' => $date->format('Y-m-d'),
            'time' => $time,
            'day_title' => $event['liturgy']['title'] ?: '',
            'day_description' => $event['liturgy']['litDetailsText'] ?: '',
            'officiating' => join("\r\n", $officiating),
            'location' => ($location ? $location->ID : ''),
        ];

        $post = [
            'post_title' => $title,
            'post_type' => PEREGRINUS_PULPIT.'_event',
            'post_status' => 'publish',
            'meta_input' => $meta,
        ];

        $id = wp_insert_post($post);
        if ($id) {
            $post = get_post($id);
            echo '<script>window.location.href="'.admin_url('post.php?post='.$id.'&action=edit').'";</script>';
        } else {
            'Fehler beim Importieren des Gottesdienstes';
        }
    }

    public static function get(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public static function findLocation($title, $locations)
    {
        $title = strtr($title, ['GZ ' => 'Gemeindezentrum ']);
        foreach ($locations as $location) {
            if ($location->post_title == $title) return $location;
        }
        return false;
    }

    public static function serviceExists($date, $time) {
        $args = [
            'post_type' => 'pulpit_event',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'date',
                    'value' => $date->format('Y-m-d'),
                ], [
                    'key' => 'time',
                    'value' => $time,
                ],
            ]
        ];
        /** @var \WP_Query $query */
        $query = new \WP_Query($args);
        return ($query->have_posts() ? $query->get_posts()[0] : false);
    }
}

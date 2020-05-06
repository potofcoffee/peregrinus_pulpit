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
use Peregrinus\Pulpit\Domain\Repository\SermonRepository;
use Peregrinus\Pulpit\Gfx\Image;
use Peregrinus\Pulpit\Gfx\ImageOverlay;

class CreateYoutubeScriptMigration extends AbstractMigration
{

    protected $title = 'Create YT script';
    protected $description = 'Creates a script to convert all mp3s to videos';

    public function execute()
    {
        global $wp_query;
        $posts = query_posts([
            'post_type' => 'pulpit_sermon',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'post_date',
            'order' => 'DESC'
        ]);

        $idx = $_GET['idx'] ?? 0;
        $post = $posts[$idx];

        $sermonRepository = new SermonRepository();
        $this->console('Processing post #' . $post->ID . ' (' . $post->post_title . ')...');
        $this->console('Permalink: <a href="'.get_permalink($post).'">'.get_permalink($post).'</a>');
        $sermon = $sermonRepository->findByID($post->ID);
        $imagePath = get_attached_file($sermon->getThumbnail());


        $events = $sermon->getEvents();
        $event = $events[count($events) - 1];
        $date = str_replace('-', '', $event->getDate());
        $destinationFile = '/home/peregrinus/youtube/' . $date . '-' . $post->post_name;

        $meta = get_post_meta($post->ID);
        $audioPath = str_replace('https://christoph-fischer.org', '', get_post($meta['audiorecording_relation'][0])->guid);
        $this->console('Audio path: '.$audioPath);

        if (($audioPath != '') && (false === strpos($audioPath, 'http:'))) {
            $audioPath = str_replace('//', '/', get_home_path() . $audioPath);
            if (false !== strpos($audioPath, ' ')) {
                $audioPathNew = '/home/peregrinus/youtube/' . $date . '.mp3';
                copy($audioPath, $audioPathNew);
                $audioPath = $audioPathNew;
                $this->console('Corrected audio path: '.$audioPath);
            }


            $d = new \DateTime($event->getDate() . ' ' . $event->getTime());

            $this->console('<textarea style="min-width: 100%">' . $post->post_title . ' (Predigt vom ' . $d->format('d.m.Y') . ', Pfarrer Christoph Fischer)</textarea>');
            $this->console('<textarea style="min-width: 100%">' . $sermon->getContent()['main'] . '</textarea>');


            $image = new Image($imagePath);
            $overlayImg = new ImageOverlay($image->getWidth(), $image->getHeight());
            $overlayImg->compositeImageFile($imagePath, \Imagick::COMPOSITE_DEFAULT, 0, 0);

            $overlayOffset = $overlayImg->getTextMetrics(
                ' ',
                PEREGRINUS_PULPIT_BASE_PATH . '/Assets/Fonts/HelveticaCondensed.ttf',
                100
            );
            //Debugger::dumpAndDie($overlayOffset);

            $overlayImg->textBlockWithBorder(
                $sermon->getTitle(),
                PEREGRINUS_PULPIT_BASE_PATH . '/Assets/Fonts/HelveticaCondensed.ttf',
                100,
                'white',
                'gray',
                $overlayOffset['textWidth'] + 2,
                -$overlayOffset['textWidth'] + 2,
                $image->getWidth() - (2 * $overlayOffset['textWidth']),
                ImageOverlay::TEXTALIGN_RIGHT

            );
            $overlayImg->writeImage($destinationFile . '.jpg');
            $this->console('Created title image ' . $destinationFile . '.jpg');

            file_put_contents($destinationFile . '.txt',
                utf8_decode($post->post_title . ' (Predigt vom ' . $d->format('d.m.Y') . ", Pfarrer Christoph Fischer)\n\n" . $sermon->getContent()['main'] . "\n\nResultat: https://christoph-fischer.org/youtube/" . basename($destinationFile) . ".avi\n\n"));

            // ffmpeg -r 1 -loop 1 -y -i /home/peregrinus/youtube/20180520-mehr-als-wunder.jpg -i https://christoph-fischer.org/wp-content/uploads/sites/2/2018/05/20180520-Mehr-als-Wunder.mp3 -shortest /home/peregrinus/youtube/20180520-mehr-als-wunder.avi
            //$commandline = 'ffmpeg -r 1 -loop 1 -y -i ' . $destinationFile . '.jpg -i "' . str_replace('//', '/', get_home_path().$audioPath) . '" -shortest ' . $destinationFile . '.avi';
            $commandline = '/bin/bash -c \'/home/peregrinus/youtube/bq "' . $destinationFile . '.jpg" "' . $audioPath . '" "' . $destinationFile . '.avi" "' . $post->post_title . '" "' . $destinationFile . '.txt"\'';

            $this->console('<textarea style="width: 100%;">' . $commandline . '</textarea>');

            exec($commandline.' > /dev/null 2>/dev/null &');

        } else {
            $this->console('Audio is not a file, but a link.');
        }
        $this->console('<a href="/wp-admin/options-general.php?page=pulpit-settings-migration&do_migration=CreateYoutubeScript&idx=' . ($idx + 1) . '">Weiter</a>');

        echo '<script>window.location.href="/wp-admin/options-general.php?page=pulpit-settings-migration&do_migration=CreateYoutubeScript&idx=' . ($idx + 1) . '";</script>';

    }


    protected function console($text)
    {
        echo $text . '<br />';
    }
}

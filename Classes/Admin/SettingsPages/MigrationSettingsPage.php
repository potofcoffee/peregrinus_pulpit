<?php /** @noinspection PhpIncludeInspection */
/** @noinspection ALL */

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

namespace Peregrinus\Pulpit\Admin\SettingsPages;

use Peregrinus\Pulpit\Domain\Model\SermonModel;

class MigrationSettingsPage extends AbstractSettingsPage
{
    public function __construct()
    {
        parent::__construct();
        $this->setPageTitle(__('Sermons: Migrations', 'pulpit'));
        $this->setMenuTitle(__('Sermons: Migrations', 'pulpit'));
    }

    public function render()
    {
    }

    private function console($text, $breaks = 1)
    {
        echo $text;
        for ($i = 1; $i <= $breaks; $i++) {
            echo '<br />';
        }
    }

    private function redirectToNext($index, $posts)
    {
        if ($index < (count($posts) - 1)) {
            $this->redirectToPost($index + 1);
        }
    }

    private function redirectToPost($index)
    {
        $url = 'https://christoph-fischer.org/wp-admin/options-general.php?page=pulpit-settings-migration&post=' . urlencode($index);
        echo('<script>window.location.href="' . $url . '";</script>');
        wp_die();
    }

    private function download(string $url, \WP_Post $post): string
    {
        $targetPath = wp_upload_dir()['path'] . '/';
        $targetFile = $targetPath . strftime('%Y%m%d-',
                strtotime($post->post_date)) . $this->sanitizeFileName($post->post_title) . '.' . pathinfo($url,
                PATHINFO_EXTENSION);
        $target = '/' . str_replace(ABSPATH, '', $targetFile);
        $this->console('Downloading ' . $url . ' to ' . $target);
        //file_put_contents($targetFile, fopen($url, 'r'));

        $fh = fopen($targetFile, 'w');
        set_time_limit(0); // unlimited max execution time
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_FILE => $fh,
            CURLOPT_TIMEOUT => 28800, // set this to 8 hours so we dont timeout on big files
            CURLOPT_URL => str_replace(' ', '%20', $url),
        ]);
        curl_exec($ch);
        curl_close($ch);
        fclose($fh);

        return $target;
    }

    private function sanitizeFileName($filename)
    {
        return strtr($filename, [
            ' ' => '-',
            'Ä' => 'Ae',
            'Ö' => 'Oe',
            'Ü' => 'Ue',
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss',
            '?' => '',
            '!' => '',
            '.' => ''
        ]);
    }

    private function attachImage($filename, \WP_Post $post, $postMeta)
    {
        $filename = substr(ABSPATH, 0, -1) . $filename;
        if (!file_exists($filename)) {
            $this->console('<b>Error:</b> Audio file not present.');
            wp_die();
        }

        $filetype = wp_check_filetype(basename($filename), null);

        $attachment = [
            'guid' => 'https://christoph-fischer.org' . $filename,
            'post_mime_type' => $filetype['type'],
            'post_title' => $post->post_title . ($postMeta['subtitle'][0] ? ': ' . $postMeta['subtitle'][0] : ''),
            'post_content' => '"' . $post->post_title . ($postMeta['subtitle'][0] ? ': ' . $postMeta['subtitle'][0] : '') . '". Titelbild zur Predigt von Christoph Fischer vom ' . strftime('%d.%m.%Y',
                    strtotime($post->post_date)) . ' (' . $postMeta['church'][0] . ')',
            'post_status' => 'inherit'
        ];
        $attachmentId = wp_insert_attachment($attachment, $filename, $post->ID);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $this->console('Created new attachment with id #' . $attachmentId);
        $attachmentMetadata = wp_generate_attachment_metadata($attachmentId, $filename);
        wp_update_attachment_metadata($attachmentId, $attachmentMetadata);
        set_post_thumbnail($post->ID, $attachmentId);
    }

    private function createAttachmentFromLocalFile($filename, \WP_Post $post, $postMeta)
    {
        $filename = substr(ABSPATH, 0, -1) . $filename;
        if (!file_exists($filename)) {
            $this->console('<b>Error:</b> Audio file not present.');
            wp_die();
        }

        $filetype = wp_check_filetype(basename($filename), null);
        $audioMeta = wp_read_audio_metadata($filename);

        $audioMeta['author'] = 'Christoph Fischer';
        $audioMeta['title'] = $post->post_title;

        $attachment = [
            'guid' => 'https://christoph-fischer.org' . $filename,
            'post_mime_type' => $filetype['type'],
            'post_title' => $post->post_title . ($postMeta['subtitle'][0] ? ': ' . $postMeta['subtitle'][0] : ''),
            'post_content' => '"' . $post->post_title . ($postMeta['subtitle'][0] ? ': ' . $postMeta['subtitle'][0] : '') . '". Aufnahme der Predigt von Christoph Fischer vom ' . strftime('%d.%m.%Y',
                    strtotime($post->post_date)) . ' (' . $postMeta['church'][0] . ')',
            'post_status' => 'inherit'
        ];
        $attachmentId = wp_insert_attachment($attachment, $filename, $post->ID);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $this->console('Created new attachment with id #' . $attachmentId);
        $attachmentMetadata = wp_generate_attachment_metadata($attachmentId, $filename);
        wp_update_attachment_metadata($attachmentId, $attachmentMetadata);
        update_post_meta($post->ID, 'audiorecording_relation', $attachmentId);
    }

    private function findSermonData(\WP_Post $post, array $sermonData): array
    {
        foreach ($sermonData as $sermon) {
            if ($sermon['title'] == $post->post_title) {
                return $sermon;
            }
        }
        return [];
    }

    private function copyLocalFile($filename, \WP_Post $post)
    {
        $target = wp_upload_dir()['path'] . '/' . strftime('%Y%m%d-',
                strtotime($post->post_date)) . $this->sanitizeFileName($post->post_title) . '.' . pathinfo($filename,
                PATHINFO_EXTENSION);
        $this->console('Copying ' . $filename . ' to ' . $target);
        copy($filename, $target);
        return (str_replace(ABSPATH, '/', $target));
    }

}

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

namespace Peregrinus\Pulpit\CustomFormats;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\SermonModel;
use Peregrinus\Pulpit\Fluid\DynamicVariableProvider;
use Peregrinus\Pulpit\View;
use Symfony\Component\Debug\Debug;

class PodcastCustomFormat extends AbstractCustomFormat
{

    protected $defaultExtension = 'xml';

    /**
     * @inheritdoc
     */
    public function register()
    {
        parent::register();

        // register as valid feed type by creating the appropriate action
        add_action('do_feed_podcast', [$this, 'render'], 10, 1);

        // add rewrite rule for podcast.xml
        add_rewrite_rule('podcast\.xml$', 'index.php?feed=podcast', 'top');

        // add header link
        add_action('wp_head', [$this, 'feedLink'], 2);
    }

    public function getPodcastOptions(): array {
        $options = [];
        foreach (['title', 'description', 'image', 'language', 'copyright', 'author_name', 'author_email', 'category'] as $key) {
            $options['podcast_'.$key] = get_option('pulpit_podcast_'.$key);
        }
        return $options;
    }

    public function feedLink() {
        // get podcast options
        echo "<!-- Podcast feed -->\r\n";
        echo '<link rel="alternate" type="application/rss+xml" title="'
            . esc_attr( get_option('pulpit_podcast_title')) . '" href="'
            . get_home_url().'/podcast.xml' . "\" />\n";
    }

    /**
     * Check whether this CustomFormat should be output
     */
    public function run()
    {
        if ($_GET['feed'] == $this->getKey()) {
            $this->render();
        }
    }


    /**
     * @inheritdoc
     */
    public function render()
    {
        $posts = get_posts(['post_type' => 'pulpit_sermon', 'numberposts' => -1, 'post_status' => 'publish']);
        $items = [];
        /** @var \WP_Post $post */
        foreach ($posts as $post) {
            $meta = get_post_meta($post->ID);
            if (((string)$meta['audiorecording_relation'][0] != '') && ($post->post_status != 'pulpit_hidden')) {
                $audioFile = get_post($meta['audiorecording_relation'][0]);
                if (basename($audioFile->guid) !== basename(get_home_url())) {
                    $audioMeta = wp_get_attachment_metadata($meta['audiorecording_relation'][0]);
                    if (!$audioMeta['filesize']) {
                        $rawFile = get_attached_file($audioFile->ID);
                        $audioMeta = wp_generate_attachment_metadata($meta[$audioFile->ID], $rawFile);
                        if (!$audioMeta['filesize']) {
                            // hackish, but ...
                            $audioMeta['filesize'] = filesize($rawFile);
                        }
                        if (!$audioMeta['mime_type']) {
                            $audioMeta['mime_type'] = 'audio/mpeg';
                        }
                    }
                    $items[] = [
                        'status' => $post->post_status,
                        'sermon' => new SermonModel($post),
                        'post' => $post,
                        'meta' => $meta,
                        'content' => get_extended(get_post_field('post_content', $post->ID)),
                        'formattedContent' => apply_filters('the_content', $post->post_content),
                        'audio' => [
                            'file' => $audioFile,
                            'meta' => $audioMeta,
                        ],
                        'url' => get_permalink($post->ID),
                        'image' => \has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID) : null,
                    ];
                }
            }
        }

        $podcastConfig = $this->getPodcastOptions();
        $podcastConfig['url'] = get_home_url().'/podcast.xml';
        $podcastConfig['podcast_category'] = explode(
            '/',
            $podcastConfig['podcast_category']
            //str_replace('&', '&amp;', $podcastConfig['podcast_category'])
        );
        $podcastConfig['image'] = [
            'file' => get_post($podcastConfig['podcast_image']),
            'meta' => get_post_meta($podcastConfig['podcast_image'])
        ];

        $view = new View();
        $context = $view->getRenderingContext();
        $context->setVariableProvider(new DynamicVariableProvider());
        $context->getTemplatePaths()->setTemplatePathAndFilename($this->getViewFilePath());

        $view->assign('items', $items);
        $view->assign('podcast', $podcastConfig);


        header('Content-Type: application/rss+xml');
        echo $view->render($this->viewName);
        exit();
    }
}

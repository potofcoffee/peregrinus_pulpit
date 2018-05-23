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

use Peregrinus\Pulpit\View;

class AbstractTemplatedCustomFormat extends AbstractCustomFormat
{

    protected $viewName = '';


    /**
     * Assign additional data to the view (to be used in descendant classes)
     * @param \WP_Post $post Post
     * @param View $view View
     */
    protected function prepareView(\WP_Post $post, View $view)
    {
        $meta = get_post_meta($post->ID);
        $thumbnail = \has_post_thumbnail($post->ID) ? \get_the_post_thumbnail($post->ID) : null;
        $thumbnailUrl = \has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url() : null;

        $context = $view->getRenderingContext();
        $context->setControllerName('CustomView');

        $content = get_extended(get_post_field('post_content', $post->ID));
        $formattedContent = apply_filters('the_content', $post->post_content);
        $author = $this->getAuthor($post);

        $view->assign('post', $post);
        $view->assign('author', $author);
        $view->assign('content', $content);
        $view->assign('formattedContent', $formattedContent);
        $view->assign('meta', $meta);
        $view->assign('underlineMode', $_GET['underlineMode'] ? $_GET['underlineMode'] : 'html5');
        $view->assign('thumbnail', $thumbnail);
        $view->assign('thumbnailUrl', $thumbnailUrl);
    }

    protected function getAuthor(\WP_Post $post): array
    {
        $author = ['ID' => $post->post_author];
        foreach (['user_login', 'user_pass', 'user_nicename', 'user_email', 'user_url', 'user_registered',
                     'user_activation_key', 'user_status', 'display_name', 'nickname', 'first_name', 'last_name',
                     'description', 'jabber', 'aim', 'yim', 'user_level', 'user_firstname', 'user_lastname',
                     'user_description', 'rich_editing', 'comment_shortcuts', 'admin_color', 'plugins_per_page', 'plugins_last_view'] as $field) {
            $author[$field] = get_the_author_meta($field, $post->post_author);
        }
        return $author;
    }

    protected function getPost(): \WP_Post
    {
        global $post;
        return $post = get_post();
    }

    /**
     * Render the CustomFormat
     */
    function render()
    {
        $post = $this->getPost();
        $view = new View();

        $this->prepareView($post, $view);
        echo $view->render($this->viewName);
        exit();
    }


}
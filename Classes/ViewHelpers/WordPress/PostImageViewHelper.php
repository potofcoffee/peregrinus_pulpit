<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2017 Christoph Fischer, http://www.peregrinus.de
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

namespace Peregrinus\Pulpit\ViewHelpers\WordPress;

use Peregrinus\Pulpit\Utility\ImageCreditsUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class PostImageViewHelper
 * Render a post image with credits
 * @package Peregrinus\Pulpit\ViewHelpers
 */
class PostImageViewHelper extends AbstractViewHelper
{

    /**
     * @var boolean
     */
    protected $escapeChildren = false;
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('post', 'int', 'Post ID', true);
        $this->registerArgument('size', 'string', 'Size', false, 'post-thumbnail');
        $this->registerArgument('showCaption', 'bool', 'Show caption', false, false);
        $this->registerArgument('link', 'string', 'Link', false);
        $this->registerArgument('target', 'string', 'Target', false);
        $this->registerArgument('class', 'string', '', false);
        $this->registerArgument('raw', 'bool', '', false, false);
        $this->registerArgument('noSize', 'bool', '', false, false);
        $this->registerArgument('noWidth', 'bool', '', false, false);
        $this->registerArgument('noHeight', 'bool', '', false, false);
    }

    /**
     * Render the output a WP function
     * @return string
     */
    public function render()
    {
        $code = get_the_post_thumbnail( $this->arguments['post'], $this->arguments['size'], [
            'class' => $this->arguments['class'] ?: 'img-responsive',
        ] );

        if ($this->arguments['noSize'] || $this->arguments['noWidth']) {
            $code = preg_replace("/width=\"(.*?)\"/", "", $code);
        }
        if ($this->arguments['noSize'] || $this->arguments['noHeight']) {
            $code = preg_replace("/height=\"(.*?)\"/", "", $code);
        }

        if ($this->arguments['showCaption']) {
            $caption = get_the_post_thumbnail_caption($this->arguments['post']);
        } else {
            $caption = '';
        }

        $attachment = get_attached_file(get_post_thumbnail_id($this->arguments['post']));
        if ($this->arguments['raw']) {
            return '<img src="'.str_replace(ABSPATH, get_site_url().'/', $attachment).'">';
        }
        $creditsUtility = new ImageCreditsUtility($attachment);
        $legal = $creditsUtility->getCredits('full');

        $legal = ( $legal ? __( 'Image', 'pulpit') . ': ' . $legal : '' );
        if ( $caption ) {
            $legal = $caption . ( $legal ? ' ' . $legal : '' );
        }
        if ( $legal ) {
            return '<!-- Image: ' . $attachment . ' -->' . PHP_EOL
                . '<!-- Source: ' . $creditsUtility->getCredits('none') . ' -->'
                .'<figure class="'.($caption ? 'image-caption' : 'image-credits').'">'
                .($this->arguments['link'] ? '<a href="'.$this->arguments['link'].'" target="'.$this->arguments['target'].'">' : '')
                . $code
                .($this->arguments['link'] ? '</a>' : '')
                . '<figcaption class="image-copyrights">' . $legal . '</figcaption></figure>';
        } else {
            return $code;
        }

    }

}

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

namespace Peregrinus\Pulpit\ViewHelpers\Pulpit\Output\Word;

use PhpOffice\PhpWord\Shared\Html;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class HtmlViewHelper
 * Provides a ViewHelper to render a HTML block into a PHPWord document
 * @package Peregrinus\Pulpit\ViewHelpers\Pulpit\Output\Word
 */
class HtmlViewHelper extends AbstractViewHelper
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
     * Render this ViewHelper
     */
    protected function render()
    {
        /** @var \PhpOffice\PhpWord\Element\Section $section */
        $section = $this->renderingContext->getVariableProvider()->get('__PhpWord_Section');

        $html = $this->renderChildren();

        // fix for blockquote
        // @TODO: provide a better, more configurable fix here that also takes care of other tags
        // and uses a stylesheet. This might be tricky, giben the nature of PHPWord's default HTML parser
        $html = strtr($html, [
            '<blockquote>' => '<span style="font-style: italic; font-size: 11pt; text-align: center;">',
            '</blockquote>' => '</span>'
        ]);

        Html::addHtml($section, $html, false, false);
    }
}

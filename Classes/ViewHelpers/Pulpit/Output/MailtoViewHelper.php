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

namespace Peregrinus\Pulpit\ViewHelpers\Pulpit\Output;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class MailtoViewHelper extends AbstractViewHelper
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
        $this->registerArgument('to', 'string', 'Address');
        $this->registerArgument('subject', 'string', 'Subject');
        $this->registerArgument('class', 'string', '');
        $this->registerArgument('style', 'string', '');
        $this->registerArgument('title', 'string', '');
        $this->registerArgument('linkTitle', 'string', 'Link title');
        $this->registerArgument('prependSpan', 'string', 'Prepend span');
    }

    protected function render()
    {
        $mailToUri = 'mailto:'.rawurlencode($this->arguments['to']).'?subject=' . rawurlencode($this->arguments['subject']);
        if ($body = $this->renderChildren()) {
            $mailToUri .= '&body=' . rawurlencode($body);
        }
        $mailToUri = '<a href="' . htmlspecialchars($mailToUri) . '"';

        foreach (['class', 'style', 'title'] as $argument) {
            if ($x = $this->arguments[$argument]) $mailToUri.= ' '.$argument.'="'.$x.'"';
        }

        $mailToUri .= '>'
            .($this->arguments['prependSpan'] ? '<span class="'.$this->arguments['prependSpan'].'"></span> ' : '')
            .$this->arguments['linkTitle'].'</a>';

        return $mailToUri;
    }
}

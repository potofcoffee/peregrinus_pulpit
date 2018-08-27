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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class TranslateViewHelper
 * Exposes WordPress' translation function __() to Fluid
 * @package Peregrinus\Pulpit\ViewHelpers
 */
class TranslateViewHelper extends AbstractViewHelper
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
        $this->registerArgument('content', 'string', 'Text to translate', false);
        $this->registerArgument('global', 'bool', 'Use global textdomain', false, false);
        $this->registerArgument('textdomain', 'string', 'textdomain', false, PEREGRINUS_PULPIT);
    }

    /**
     * Render the output using __()
     * @return string
     */
    public function render()
    {
        if (!isset($this->arguments['content'])) {
            $content = $this->renderChildren();
        } else {
            $content = $this->arguments['content'];
        }
        if (($this->arguments['global'])) {
            return __($content);
        } else {
            return __($content, $this->arguments['textdomain']);
        }
    }

}

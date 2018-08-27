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
 * Class PermalinkViewHelper
 * Exposes WordPress' permalink function to Fluid
 * @package Peregrinus\Pulpit\ViewHelpers
 */
class PermalinkViewHelper extends AbstractViewHelper
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
        $this->registerArgument('id', 'string', 'Post ID', false);
    }

    /**
     * Render the output a WP function
     * @return string
     */
    public function render()
    {
        return get_the_permalink($this->arguments['id'] ?: $this->renderChildren());
    }

}

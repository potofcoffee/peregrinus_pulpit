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


namespace Peregrinus\Pulpit\ViewHelpers;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class AbstractBufferedViewHelper
 * Provide buffer functions to handle WordPress' habit to directly output using echo()
 * @package Peregrinus\Pulpit\ViewHelpers
 */
class AbstractBufferedViewHelper extends AbstractViewHelper
{


    /**
     * Render the ViewHelper
     * @return string Output
     */
    public function render()
    {
        ob_start();
        $this->renderBuffered();
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * Render functionality. This function should be overriden.
     */
    protected function renderBuffered()
    {
    }

}
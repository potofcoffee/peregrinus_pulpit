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

namespace Peregrinus\Pulpit\ViewHelpers\Pulpit\Output\Word\Styles;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use PhpOffice\PhpWord\PhpWord;

class TitleViewHelper extends AbstractViewHelper
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
        $this->registerArgument('depth', 'string', 'Depth');
        $this->registerArgument('font', 'array', 'Font style');
        $this->registerArgument('paragraph', 'array', 'Paragraph style');
    }

    protected function render()
    {
        $phpWord = $this->renderingContext->getVariableProvider()->get('__PhpWord');
        $phpWord->addTitleStyle($this->arguments['depth'], $this->arguments['font'] ?: [], $this->arguments['paragraph'] ?: []);
    }

}

<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2019 Christoph Fischer, http://www.peregrinus.de
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

namespace Peregrinus\Pulpit\ViewHelpers\Pulpit\Content;

use Peregrinus\Pulpit\Content\BibleText;
use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Service\ReferenceParserService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class BibleViewHelper
 *
 * Render p:bible ViewHelper
 * Example:
 * <p:content.bible reference="<reference>" [optional="1"] [var="myVar"]>
 *
 * <reference> (required) string Bible reference, which may include optional verses in parentheses
 *                               (e.g. "Jesaja 42,1-4 (5-9)")
 * <optional>  bool If true, the optional verses will be returned instead
 *
 * @source Peregrinus\Portal\ViewHelpers\Portal\BibleViewHelper
 */
class BibleViewHelper extends AbstractViewHelper
{

    protected $escapeChildren = false;
    protected $escapeOutput = false;

    /**
     * Return rendered Bible text as array of individual verses
     * @return array Verses
     */
    public function render(): array
    {
        $reference = ReferenceParserService::getInstance()->parse($this->renderChildren(), $this->arguments['optional']);
        $bibleText = new BibleText($this->arguments['version']);
        return $bibleText->get($reference);
    }

    public function initializeArguments()
    {
        parent::initializeArguments(); // TODO: Change the autogenerated stub
        $this->registerArgument('version', 'string', '', false, 'LUT17');
        $this->registerArgument('optional', 'bool', '', false, false);
    }


}

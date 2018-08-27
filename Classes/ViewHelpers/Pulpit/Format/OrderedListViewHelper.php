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

namespace Peregrinus\Pulpit\ViewHelpers\Pulpit\Format;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class OrderedListViewHelper extends AbstractViewHelper
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
        $this->registerArgument('list', 'string', 'The list');
        $this->registerArgument('underlineMode', 'string', 'Underline mode');
        $this->registerArgument('listStyle', 'string', 'List style');
        $this->registerArgument('itemStyle', 'string', 'Item style');
        $this->registerArgument('breakAfter', 'string', 'Break after');
    }

    /**
     * Renders a text as an ordered list (one item per line)
     *
     * @param string $list The number of characters of the dummy content
     * @validate $length StringValidator
     * @param string $underlineMode
     * @param string $listStyle
     * @param string $itemStyle
     * @param string $breakAfter
     * @return string as ordered list
     * @author Christoph Fischer <christoph.fischer@volksmission.de>
     */
    public function render()
    {
        $underlineMode = $this->arguments['underlineMode'] ? $this->arguments['underlineMode'] : 'html5';
        $listStyle = $this->arguments['listStyle'];
        $itemStyle = $this->arguments['itemStyle'];
        $breakAfter = $this->arguments['breakafter'] ? $this->arguments['breakafter'] : false;

        if (!$this->arguments['list']) {
            $this->arguments['list'] = $this->renderChildren();
        }
        $this->arguments['list'] = str_replace(['<u>', '</u>'], ['[', ']'], $this->arguments['list']);
        switch ($underlineMode) {
            case 'blank':
                $this->arguments['list'] = str_replace(
                    ['[', ']'],
                    ['<span style="color: white; border-bottom: solid 1px black">', '</span>'],
                    $this->arguments['list']
                );
                break;
            case 'html5':
                $this->arguments['list'] = str_replace(
                    ['[', ']'],
                    ['<span style="text-decoration: underline;">', '</span>'],
                    $this->arguments['list']
                );
                break;
            case 'remove':
                $this->arguments['list'] = str_replace(
                    ['[', ']'],
                    ['', ''],
                    $this->arguments['list']
                );
                break;
        }
        $items = explode("\r", $this->arguments['list']);
        foreach ($items as $key => $item) {
            if (!trim($item)) {
                unset($items[$key]);
            }
        }
        if (is_array($items)) {
            return str_replace(
                '<li></li>',
                '',
                '<ol ' . ($listStyle ? 'style="' . $listStyle . '"' : '') . '><li ' . ($itemStyle ? 'style="'
                . $itemStyle . '"' : '') . '>'
                . join('</li><li ' . ($itemStyle ? 'style="' . $itemStyle . '"' : '') . '>', $items)
                . ($breakAfter ? '<br />' : '') . '</li></ol>'
            );
        } else {
            return $this->arguments['list'];
        }
    }
}

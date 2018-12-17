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

namespace Peregrinus\Pulpit\ViewHelpers\Pulpit\Content;

use Peregrinus\Pulpit\Debugger;

class SongViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
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
        $this->registerArgument('song', 'array', 'Song');
    }

    protected function render()
    {
        $verses = [];
        foreach (explode(',', str_replace('+', ',', $this->arguments['song']['verses'])) as $verseRange) {
            $verseRange = trim($verseRange);
            $range = explode('-',$verseRange);
            if (!isset($range[1])) $range[1] = $range[0];
            for ($i=$range[0]; $i<= $range[1]; $i++) {
                $verses[] = $i;
            }

        }

        $o = '';
        foreach ($verses as $verse) {
            $o .= '<p>'.$verse.'. '.nl2br($this->arguments['song']['fulltext'][$verse]).'</p>';
        }

        return $o;
    }
}

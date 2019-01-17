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
use Peregrinus\Pulpit\Domain\Model\SongModel;
use Symfony\Component\Debug\Debug;

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
        $this->registerArgument('song', 'mixed', 'Song');
        $this->registerArgument('numbered', 'bool', 'Number verses?', false, true);
        $this->registerArgument('tab', 'string', 'Tab string (for indentation)', false, "\t");
    }

    protected function render()
    {
        /** @var SongModel $song */
        $song = $this->arguments['song']['song'];

        $verses = [];

        if ($this->arguments['song']['verses']) {
            foreach (explode(',', str_replace('+', ',', $this->arguments['song']['verses'])) as $verseRange) {
                $verseRange = trim($verseRange);
                $range = explode('-', $verseRange);
                if (!isset($range[1])) {
                    $range[1] = $range[0];
                }
                for ($i = $range[0]; $i <= $range[1]; $i++) {
                    $verses[] = $i;
                }
            }
        } else {
            $verses = array_keys($song->getVerses());
            if (is_array($verses)) unset($verses[0]);
            if (count($verses)==1) $this->arguments['numbered'] = false;
        }

        $o = '';
        foreach ($verses as $verse) {
            if ($this->arguments['tab'] == "\t") {
                $o .= '<p>'
                    . ($this->arguments['numbered'] ? $verse . '. ' : '')
                    . nl2br(strtr($song->getVerses()[$verse],
                        ['|' => "\n", '>>' => "\t"]))
                    . '</p>';
            } else {
                $text = nl2br(strtr($song->getVerses()[$verse], ['|' => "\n", '>>' => '']));
                $o .= '<p style="margin-left: '.$this->arguments['tab']
                    .'; text-indent: -'.$this->arguments['tab'].'">'.$text.'</p>';
            }
        }

        return $o;
    }
}

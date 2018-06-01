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


namespace Peregrinus\Pulpit\ViewHelpers\Pulpit;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class YouversionLinkViewHelper extends AbstractViewHelper
{

    /**
     * @var boolean
     */
    protected $escapeChildren = false;
    /**
     * @var boolean
     */
    protected $escapeOutput = false;
    private $OSISNames = array(
        'GEN' => 'Genesis',
        'EXO' => 'Exodus',
        'LEV' => 'Leviticus',
        'NUM' => 'Numeri',
        'DEU' => 'Deuteronomium',
        'JOS' => 'Josua',
        'JDG' => 'Richter',
        'RUT' => 'Ruth',
        '1SA' => '1.Samuel',
        '2SA' => '2.Samuel',
        '1KI' => '1.Könige',
        '2KI' => '2.Könige',
        '1CH' => '1.Chronik',
        '2CH' => '2.Chronik',
        'EZR' => 'Esra',
        'NEH' => 'Nehemia',
        'EST' => 'Esther',
        'JOB' => 'Hiob',
        'PSA' => 'Psalm',
        'PRO' => 'Sprüche',
        'ECC' => 'Prediger',
        'SNG' => 'Hohelied',
        'ISA' => 'Jesaja',
        'JER' => 'Jeremia',
        'LAM' => 'Klagelieder',
        'EZK' => 'Hesekiel',
        'DAN' => 'Daniel',
        'HOS' => 'Hosea',
        'JOL' => 'Joel',
        'AMO' => 'Amos',
        'OBA' => 'Obadja',
        'JON' => 'Jona',
        'MIC' => 'Micha',
        'NAH' => 'Nahum',
        'HAB' => 'Habakkuk',
        'ZEP' => 'Zefanja',
        'HAG' => 'Haggai',
        'ZEC' => 'Sachraja',
        'MAL' => 'Maleachi',
        'MAT' => 'Matthäus',
        'MAR' => 'Markus',
        'LUK' => 'Lukas',
        'JHN' => 'Johannes',
        'ACT' => 'Apostelgeschichte',
        'ROM' => 'Römer',
        '1CO' => '1.Korinther',
        '2CO' => '2.Korinther',
        'GAL' => 'Galater',
        'EPH' => 'Epheser',
        'PHP' => 'Philipper',
        'COL' => 'Kolosser',
        '1TH' => '1.Thessalonicher',
        '2TH' => '2.Thessalonicher',
        '1TI' => '1.Timotheus',
        '2TI' => '2.Timotheus',
        'TIT' => 'Titus',
        'PHM' => 'Philemon',
        'HEB' => 'Hebräer',
        'JAS' => 'Jakobus',
        '1PE' => '1.Petrus',
        '2PE' => '2.Petrus',
        '1JN' => '1.Johannes',
        '2JN' => '2.Johannes',
        '3JN' => '3.Johannes',
        'JUD' => 'Judas',
        'REV' => 'Offenbarung',
    );

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('reference', 'string', 'Reference');
    }

    /**
     * Renders a bible reference as a qr code for YouVersion
     *
     * @return rendered tag
     * @author Christoph Fischer <christoph.fischer@volksmission.de>
     */
    public function render()
    {
        $reference = $this->arguments['reference'] ? $this->arguments['reference'] : $this->renderChildren();

        $refs = explode(';', $reference);
        if (is_array($refs)) {
            $ref = $refs[0];
        } else {
            $ref = $refs;
        }

        // remove all verse ranges (we only link to the first verse)
        if (strpos($ref, '-') !== false) {
            $ref = trim(substr($ref, 0, strpos($ref, '-')));
        }
        if (strpos($ref, '.') !== false) {
            $ref = trim(substr($ref, 0, strpos($ref, '.')));
        }

        // replace all separators with a dot
        $ref = str_replace(',', '.', $ref);
        $ref = str_replace(':', '.', $ref);
        $ref = str_replace(' ', '.', $ref);

        // replace book name with OSIS code
        $tmp = explode('.', $ref);
        $ref = str_replace($tmp[0], array_search($tmp[0], $this->OSISNames), $ref);

        return 'youversion://bible?reference=' . $ref;
    }


}
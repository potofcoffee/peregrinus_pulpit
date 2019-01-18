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

namespace Peregrinus\Pulpit\Content;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Service\ReferenceParserService;

class BibleText
{

    protected $version = '';
    protected $text = [];
    protected $renderedVerses = [];

    public function __construct($version = 'LUT17')
    {
        $this->version=$version;
        $textFile = PEREGRINUS_PULPIT_BASE_PATH.'Assets/Bible/'.strtolower($version).'.txt';
        if (file_exists($textFile)) $this->text = explode("\n", file_get_contents($textFile));
    }

    public function get(array $reference): array {
        $result = [];
        $this->renderedVerses = [];
        foreach ($reference['parsed'] as $section) {
            $section['text'] = $this->getSection($section['range']);
            $result[] = $section;
        }
        return $result;
    }

    public function getSection(array $reference): array {
        $copy = false;
        $output = [];
        foreach ($this->text as $line) {
            if ($this->matches($line, $reference[0])) $copy = true;
            if ($reference[1]['verse']=='') {
                if ($copy && (!$this->matches($line, $reference[1]))) return $output;
                $data = $this->parse($line);
                if ($copy && (!isset($this->renderedVerses[$data['referenceKey']]))) {
                    $output[$data['referenceKey']] = $data;
                    $this->renderedVerses[$data['referenceKey']] = $data;
                }
            } else {
                $data = $this->parse($line);
                if ($copy && (!isset($this->renderedVerses[$data['referenceKey']]))) {
                    $output[$data['referenceKey']] = $data;
                    $this->renderedVerses[$data['referenceKey']] = $data;
                }
                if ($this->matches($line, $reference[1])) return $output;
            }
        }
        return $output;
    }

    protected function parse(string $line): array {
        $tmp = explode(' ', $line);
        $book = $tmp[0];
        list($chapter, $verse) = explode(':',$tmp[1]);
        $slug = $book.' '.$chapter.':'.$verse;
        $bookTitle = ReferenceParserService::getInstance()->getBookTitle($book);
        return [
            'book' => $book,
            'bookTitle' => $bookTitle,
            'referenceKey' => $bookTitle.'|'.$chapter.'|'.$verse,
            'referenceText' => $bookTitle.' '.$chapter.','.$verse,
            'chapter' => $chapter,
            'verse' => $verse,
            'text' => str_replace($slug.' ', '', $line),
            'slug' => $slug,
        ];
    }

    protected function matches(string $line, array $reference): bool {
        $ref = $reference['book'].' '.$reference['chapter'].':'.$reference['verse'];
        return (strtolower(substr($line, 0, strlen($ref))) == strtolower($ref));
    }

}

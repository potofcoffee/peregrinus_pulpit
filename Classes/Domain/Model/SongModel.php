<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2018 Christoph Fischer, http://www.peregrinus.de
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

namespace Peregrinus\Pulpit\Domain\Model;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Repository\AgendaRepository;
use Peregrinus\Pulpit\Taxonomies\Song\SongbookTaxonomy;
use Symfony\Component\Debug\Debug;

class SongModel extends AbstractModel
{
    public function __construct(\WP_Post $post)
    {
        parent::__construct($post);

        $tax = new SongbookTaxonomy();

        // add songbooks to meta
        $songbookTerms = get_the_terms($post->ID, $tax->getName());
        foreach ($songbookTerms as $term) {
            $songbooks[] = array_merge((array)$term, $tax->getMeta($term));

        }
        $this->setMetaElement('songbooks', $songbooks);

        // add verses to meta
        if (false !== strpos($post->post_content, '</li>')) {
            preg_match_all('/<li>(.*?)<\/li>/is', $post->post_content, $rawVerses);
            array_unshift($rawVerses[1], '');
            $this->setMetaElement('verses', $rawVerses[1]);
        } else {
            $this->setMetaElement('verses', [ 0 => $post->post_content]);
        }
    }

    public function getNameAndNumber(): string {
        return isset($this->getSongbooks()[0]) ? $this->getSongbooks()[0]['abbreviation'].' '.$this->getNumber().' '.$this->getTitle() : '';
    }


    public function getFromSongBook(): bool {
        return true;
    }

}

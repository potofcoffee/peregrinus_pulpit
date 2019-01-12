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

namespace Peregrinus\Pulpit\Service;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\SongModel;

class SongService
{

    /** @var SongService */
    protected static $instance = null;

    protected $data = [];

    /**
     * SongService constructor.
     */
    public function __construct()
    {
        foreach (get_posts(['post_type' => 'pulpit_song', 'posts_per_page' => -1]) as $post) {
            $song = new SongModel($post);
            $this->data[$song->getID()] = $song;
        }
    }

    public static function getInstance(): SongService
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function selectBox($id, $name, $value)
    {
        // allow user entries:
        $data = $this->data;
        if (!isset($data[$value])) {
            $data[$value] = $value;
        }

        $o = '<select class="pulpit-song-selectbox" style="width: 100%;" name="' . $name . '" id="' . $id . '">';
        /**
         * @var int $number
         * @var SongModel $song
         */
        foreach ($data as $id => $song) {
            if (is_string($song)) {
                $o .= '<option '.($song == $value ? ' selected' : '').'>'.$song.'</option>';
            } else {
                $o .= '<option value="' . $id . '" '
                    . ($value == $id ? ' selected' : '') . '>'
                    . $song->getNameAndNumber()
                    . '</option>';
            }
        }
        $o .= '</select>';
        return $o;
    }

    public function get($number) {
        return $this->data[$number] ?: $number;
    }

    public function renderSinglePreview($value) {
        return isset($this->data[$value]) ? $this->data[$value]->getNameAndNumber() : $value;
    }

}

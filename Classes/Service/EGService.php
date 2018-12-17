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

class EGService
{

    /** @var EGService */
    protected static $instance = null;

    protected $data = [];

    /**
     * EGService constructor.
     */
    public function __construct()
    {
        $this->data = yaml_parse_file(PEREGRINUS_PULPIT_BASE_PATH . 'Assets/Songbooks/EG.yaml');
    }

    public static function getInstance(): EGService
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
        foreach ($data as $number => $song) {
            $o .= '<option'
                . ($number != $song['title'] ? ' value="' . $number . '"' : '')
                . ($value == $number ? ' selected' : '') . '>'
                . ($number != $song['title'] ? $number . ' ' . $song['title'] : $song['title'])
                . '</option>';
        }
        $o .= '</select>';
        return $o;
    }

    public function get($number) {
        return $this->data[$number] ?: $number;
    }

    public function renderSinglePreview($number) {
        $data = $this->data;
        if (!isset($data[$number])) {
            $data[$number] = $number;
        }
        $song = $data[$number];
        return (is_array($song) ? $number . ' ' . $song['title'] : $song);
    }

}

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

namespace Peregrinus\Pulpit\AgendaItems;

class AgendaItemFactory
{

    public static function get($key)
    {
        $class = 'Peregrinus\\Pulpit\\AgendaItems\\'.ucfirst($key).'AgendaItem';
        if (class_exists($class)) return new $class(); else return null;
    }

    /**
     * Get all AgendaItems
     * @return array Instances of each AgendaItem
     */
    public static function getAll()
    {
        foreach (glob(PEREGRINUS_PULPIT_CLASS_PATH . '/AgendaItems/*AgendaItem.*') as $class) {
            $baseClass = pathinfo($class, PATHINFO_FILENAME);
            $class = 'Peregrinus\\Pulpit\\AgendaItems\\' . $baseClass;
            if (substr($baseClass, 0, 8) !== 'Abstract') {
                $objects[] = new $class();
            }
        }
        return $objects;
    }

    /**
     * Render a select input with all types of AgendaItems
     * @param $id
     * @param $name
     * @param $value
     * @return string
     */
    public static function selectBox($id, $name, $value)
    {
        $o = '<select style="width: 100%" id="' . $id . '" name="' . $name . '">';

        /** @var AbstractAgendaItem $itemType */
        foreach (self::getAll() as $itemType) {
            $o .= '<option value="' . $itemType->getKey() . '" '
                . ($itemType->getKey() == $value ? 'selected' : '') . '>'
                . $itemType->getTitle()
                . '</option>';
        }

        $o .= '</select>';
        return $o;
    }
}

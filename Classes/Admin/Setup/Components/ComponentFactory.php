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

namespace Peregrinus\Pulpit\Admin\Setup\Components;

class ComponentFactory
{

    public static function get($key)
    {
        $class = 'Peregrinus\\Pulpit\\Admin\\Setup\\Components\\'.ucfirst($key).'Component';
        return new $class();
    }

    /**
     * Get all Components
     * @return array Instances of each Component
     */
    public static function getAll()
    {
        foreach (glob(PEREGRINUS_PULPIT_CLASS_PATH . 'Admin/Setup/Components/*Component.*') as $class) {
            $baseClass = pathinfo($class, PATHINFO_FILENAME);
            $class = 'Peregrinus\\Pulpit\\Admin\\Setup\\Components\\' . $baseClass;
            if (substr($baseClass, 0, 8) !== 'Abstract') {
                $objects[] = new $class();
            }
        }
        return $objects;
    }

}

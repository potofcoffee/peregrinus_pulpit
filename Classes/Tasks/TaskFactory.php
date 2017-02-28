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


namespace Peregrinus\Pulpit\Tasks;

/**
 * Class TaskFactory
 * Provides easy access to all custom tasks in this plugin.
 * @package Peregrinus\Pulpit\Tasks
 */
class TaskFactory {

	/**
	 * Get all Tasks
	 * @return array Instances of each Task
	 */
	public static function getAll() {
		foreach ( glob( PEREGRINUS_PULPIT_CLASS_PATH . '/Tasks/*Task.*' ) as $class ) {
			$baseClass = pathinfo($class, PATHINFO_FILENAME);
			$class = 'Peregrinus\\Pulpit\\Tasks\\'.$baseClass;
			if ( substr( $baseClass, 0, 8 ) !== 'Abstract' ) {
				$objects[] = new $class();
			}
		}
		return $objects;
	}


}
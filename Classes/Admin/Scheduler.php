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


namespace Peregrinus\Pulpit\Admin;

use Peregrinus\Pulpit\Tasks\TaskFactory;

/**
 * Class Scheduler
 * Provides all function relating to wp-cron.php
 * @package Peregrinus\Pulpit\Admin
 */
class Scheduler {

	/**
	 * Scheduler constructor.
	 */
	public function __construct() {
	}

	/**
	 * register the necessary hooks
	 */
	public function register() {
		// register schedules
		add_filter( 'cron_schedules', [$this, 'registerCustomIntervals']);

		// register tasks
		foreach (TaskFactory::getAll() as $task) {
			$task->register();
		}
	}


	/**
	 * Register custom intervals
	 */
	public function registerCustomIntervals() {
		return [
			'one_minute' => [
				'interval' => 60,
				'display'  => __('Every minute', PEREGRINUS_PULPIT),
			],
			'five_minutes' => [
				'interval' => 300,
				'display'  => __('Every five minutes', PEREGRINUS_PULPIT),
			]
		];
	}
}
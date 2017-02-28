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
 * Class AbstractTask
 * Contains all basic functionality for custom tasks. Any custom Task should extend this class.
 * @package Peregrinus\Pulpit\Admin\Tasks
 */
class AbstractTask {

	protected $schedule = '';

	/**
	 * AbstractTask constructor.
	 * This will register the run() method as a hook to be used by the scheduler
	 */
	public function __construct() {
		// register the hook
		\add_action($this->getHookName(), [$this, 'run']);
	}

	/**
	 * Get the key for this Task
	 * @return string
	 */
	private function getKey() {
		return lcfirst( str_replace( 'Task', '', array_pop( explode( '\\', get_class( $this ) ) ) ) );
	}

	/**
	 * Get the hook name
	 * @return string Hook name
	 */
	private function getHookName() {
		return PEREGRINUS_PULPIT.'_task_'.$this->getKey();
	}

	/**
	 * Register this task
	 */
	public function register() {
		$hook = $this->getHookName();
		if (!\wp_next_scheduled($hook)) {
			\wp_schedule_event(time(), $this->getSchedule(), $hook);
		}
	}

	/**
	 * Run the task
	 * This is the main function that invokes this task's logic
	 */
	public function run() {

	}

	/**
	 * Get this task's schedule
	 * @return string Schedule
	 */
	public function getSchedule() {
		return $this->schedule;
	}

	/**
	 * Set this tasks's schedule
	 * @param string $schedule Schedule
	 */
	public function setSchedule( $schedule ) {
		$this->schedule = $schedule;
	}


}
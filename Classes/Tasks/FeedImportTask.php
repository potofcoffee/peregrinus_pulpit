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

use Peregrinus\Pulpit\Import\Feed;

/**
 * Class FeedImportTask
 * Custom task to import sermons from a remote feed
 * @package Peregrinus\Pulpit\Tasks
 */
class FeedImportTask extends AbstractTask {
	protected $schedule = 'hourly';

	/**
	 * Run the FeedImport task
	 */
	public function run() {
		__log( $this, 'Runner started' );
		$option = get_option( PEREGRINUS_PULPIT . '_general' );
		__log( $this, 'Got option', $option );
		if ( $option['feed'] ) {
			$feed = new Feed( $option['feed'], $option['church'], $option['church_url'] );
			__log( $this, 'Created feed', $feed );
			$feed->import();
		} else {
			__log( $this, 'No feed configured.' );
		}
		__log( $this, 'Runner terminated' );
	}

}
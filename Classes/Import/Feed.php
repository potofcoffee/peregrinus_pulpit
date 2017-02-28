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


namespace Peregrinus\Pulpit\Import;

/**
 * Class Feed
 * Import from vmfds_sermons feed
 * @package Peregrinus\Pulpit\Import
 */
class Feed {

	protected $url = '';

	/**
	 * Feed constructor.
	 *
	 * @param string $url Feed url
	 */
	public function __construct( $url ) {
		$this->setUrl( $url );
		__log( $this, 'New feed from ' . $url );
	}

	/**
	 * Run the import
	 */
	public function import() {
		__log( $this, 'Starting import' );

		$rawData = file_get_contents( $this->getUrl() );
		$data    = json_decode( $rawData, true );

		$host = parse_url( $this->getUrl(), PHP_URL_HOST );

		foreach ( $data['sermons'] as $item ) {
			$sermon = new \Peregrinus\Pulpit\Import\Sermon( $item, $host );
			$sermon->convert();
			__log( $this,
				'Importing sermon (' . $sermon->getData()['preached']->format( 'd.m.Y' ) . ') "' . $sermon->getData()['title'] . '"' );
			$postId = $sermon->post();
		}

		__log( $this, 'Import done' );
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl( $url ) {
		$this->url = $url;
	}


}
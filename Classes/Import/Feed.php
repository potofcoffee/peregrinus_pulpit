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
	protected $church = '';
	protected $churchUrl = '';

	/**
	 * Feed constructor.
	 *
	 * @param string $url Feed url
	 * @param string $church Church name
	 * @param string $churchUrl Church URL
	 */
	public function __construct( $url, $church, $churchUrl ) {
		__log( $this, 'Constructor', $this );
		$this->setUrl( $url );
		$this->setChurch( $church );
		$this->setChurchUrl( $churchUrl );
		__log( $this, 'New feed from ' . $url . ' (' . $church . ', ' . $churchUrl . ')' );
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
			$sermon->setChurchData( $this->getChurch(), $this->getChurchUrl() );
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

	/**
	 * @return string
	 */
	public function getChurch() {
		return $this->church;
	}

	/**
	 * @param string $church
	 */
	public function setChurch( $church ) {
		$this->church = $church;
	}

	/**
	 * @return string
	 */
	public function getChurchUrl() {
		return $this->churchUrl;
	}

	/**
	 * @param string $churchUrl
	 */
	public function setChurchUrl( $churchUrl ) {
		$this->churchUrl = $churchUrl;
	}


}
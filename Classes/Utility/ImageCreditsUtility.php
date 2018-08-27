<?php
/*
 * breeeeathe
 *
 * Copyright (c) 2017 Christoph Fischer, http://christoph-fischer.org
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


namespace Peregrinus\Pulpit\Utility;

use Peregrinus\Pulpit\Utility\IPTCUtility;

/**
 * Class ImageCreditsUtility
 * @source vmfds_legalese
 * Copyright (c) 2016 Volksmission Freudenstadt, http://www.volksmission-freudenstadt.de
 * @author Christoph Fischer, chris@toph.de
 * @package Peregrinus\Breeeeathe\Utilities
 */
class ImageCreditsUtility {
	protected $meta = null;

	function __construct( $image ) {
		$this->meta = new IPTCUtility( $image );
	}

	function getPhotographer() {
		$tmp = explode( ' // ', $this->meta->get( 116 ) );
		unset ( $tmp[ count( $tmp ) - 1 ] );

		return join( ',', $tmp );
	}

	function getLicense() {
		return $this->inferLicense( $this->getCredits( 'none' ), 'none' );
	}

	private function inferLicense( $credits, $linkFormat ) {
		$license = '';
		if ( strpos( $credits, ' // ' ) !== false ) {
			$tmp     = explode( ' // Lizenz: ', $credits );
			$license = $tmp[1];
		} else {
			if ( strpos( $credits, ' / ' ) !== false ) {
				$tmp  = explode( ' / ', $credits );
				$site = $tmp[0];
			} else {
				$site = $credits;
			}
			switch ( $site ) {
				case 'pixabay':
					$license = 'CC0';
					break;
				case 'freeimages':
				case 'FreeImages.com':
					$license = 'FreeImages.com CL';
					break;
				case 'pixelio':
				case 'pixelio.de':
					$license = 'Pixelio NB';
					break;
				case 'OpenStreetMap':
					$license = 'ODbL 1.0';
					break;
				case 'Christoph Fischer':
				case 'cf':
					$license = 'CC-BY-SA 4.0';
			}
		}

		return $license;
	}

	function getCredits( $linkFormat = 'full' ) {
		$tmp = explode( ',', $this->meta->get( 116 ) );
		unset ( $tmp[ count( $tmp ) - 1 ] );
		$credits = join( ',', $tmp );

		$credits = $this->linkCredits( $credits, $linkFormat );

		return $credits;
	}

	private function linkCredits( $credits, $linkFormat ) {
		$license = $this->inferLicense( $credits, $linkFormat );
		if ( $license ) {
			$credits = str_replace( ' // '.__('License', 'pulpit').': ' . $license, '', $credits );
		}
		$o = $credits;

		$source = $this->meta->get( 115 );
		if ( $source ) {
			$o = $this->formatLink( $linkFormat, $source, $credits, '_blank', 'image-credits-source-link',
				__('See original image', 'pulpit') );
		}

		if ( $license ) {
			$o .= ', '.__('License', 'pulpit').': ' . $this->linkLicense( $license, $linkFormat );
		}

		return $o;
	}

	private function formatLink( $linkFormat, $url, $text, $target = '_blank', $class = '', $title = '' ) {
		$link = $text;
		switch ( $linkFormat ) {
			case 'full':
				$link = '<a href="' . $url . '" target="' . $target . '" '
				        . ( $class ? 'class="' . $class . '" ' : '' )
				        . ( $title ? 'title="' . $title . '" ' : '' ) . '>'
				        . $text
				        . '</a>';
				break;
			case 'text':
				$link = $text . ' (' . $url . ')';
				break;
			case 'raw':
				$link = $url;
				break;
		}

		return $link;
	}

	private function linkLicense( $license, $linkFormat ) {
		$url = [
			'CC0'               => 'https://creativecommons.org/publicdomain/zero/1.0/deed.de',
			'FreeImages.com CL' => 'http://de.freeimages.com/license',
			'Pixelio NB'        => 'http://www.pixelio.de/static/nutzungsbedingungen',
			'ODbL 1.0'          => 'http://opendatacommons.org/licenses/odbl/1.0/',
			'CC-BY-SA 4.0'      => 'https://creativecommons.org/licenses/by-sa/4.0/deed.de',
		];

		return ( $url[ $license ] ? $this->formatLink( $linkFormat, $url[ $license ], $license, '_blank',
			'image-credits-license-link', 'Informationen zur Lizenz' ) : $license );
	}

	function getLicenseUrl() {
		return $this->inferLicense( $this->getCredits( 'none' ), 'raw' );
	}
}

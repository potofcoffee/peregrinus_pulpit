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

DEFINE( 'IPTC_OBJECT_NAME', '005' );
DEFINE( 'IPTC_EDIT_STATUS', '007' );
DEFINE( 'IPTC_PRIORITY', '010' );
DEFINE( 'IPTC_CATEGORY', '015' );
DEFINE( 'IPTC_SUPPLEMENTAL_CATEGORY', '020' );
DEFINE( 'IPTC_FIXTURE_IDENTIFIER', '022' );
DEFINE( 'IPTC_KEYWORDS', '025' );
DEFINE( 'IPTC_RELEASE_DATE', '030' );
DEFINE( 'IPTC_RELEASE_TIME', '035' );
DEFINE( 'IPTC_SPECIAL_INSTRUCTIONS', '040' );
DEFINE( 'IPTC_REFERENCE_SERVICE', '045' );
DEFINE( 'IPTC_REFERENCE_DATE', '047' );
DEFINE( 'IPTC_REFERENCE_NUMBER', '050' );
DEFINE( 'IPTC_CREATED_DATE', '055' );
DEFINE( 'IPTC_CREATED_TIME', '060' );
DEFINE( 'IPTC_ORIGINATING_PROGRAM', '065' );
DEFINE( 'IPTC_PROGRAM_VERSION', '070' );
DEFINE( 'IPTC_OBJECT_CYCLE', '075' );
DEFINE( 'IPTC_BYLINE', '080' );
DEFINE( 'IPTC_BYLINE_TITLE', '085' );
DEFINE( 'IPTC_CITY', '090' );
DEFINE( 'IPTC_PROVINCE_STATE', '095' );
DEFINE( 'IPTC_COUNTRY_CODE', '100' );
DEFINE( 'IPTC_COUNTRY', '101' );
DEFINE( 'IPTC_ORIGINAL_TRANSMISSION_REFERENCE', '103' );
DEFINE( 'IPTC_HEADLINE', '105' );
DEFINE( 'IPTC_CREDIT', '110' );
DEFINE( 'IPTC_SOURCE', '115' );
DEFINE( 'IPTC_COPYRIGHT_STRING', '116' );
DEFINE( 'IPTC_CAPTION', '120' );
DEFINE( 'IPTC_LOCAL_CAPTION', '121' );

/**
 * Class IPTCUtility
 * @source vmfds_legalese
 * Copyright (c) 2016 Volksmission Freudenstadt, http://www.volksmission-freudenstadt.de
 * Author: Christoph Fischer, chris@toph.de
 * @package Peregrinus\Breeeeathe\Utilities
 */
class IPTCUtility {

	const IPTC_OBJECT_NAME = '005 ';
	const IPTC_EDIT_STATUS = '007 ';
	const IPTC_PRIORITY = '010 ';
	const IPTC_CATEGORY = '015 ';
	const IPTC_SUPPLEMENTAL_CATEGORY = '020 ';
	const IPTC_FIXTURE_IDENTIFIER = '022 ';
	const IPTC_KEYWORDS = '025 ';
	const IPTC_RELEASE_DATE = '030 ';
	const IPTC_RELEASE_TIME = '035 ';
	const IPTC_SPECIAL_INSTRUCTIONS = '040 ';
	const IPTC_REFERENCE_SERVICE = '045 ';
	const IPTC_REFERENCE_DATE = '047 ';
	const IPTC_REFERENCE_NUMBER = '050 ';
	const IPTC_CREATED_DATE = '055 ';
	const IPTC_CREATED_TIME = '060 ';
	const IPTC_ORIGINATING_PROGRAM = '065 ';
	const IPTC_PROGRAM_VERSION = '070 ';
	const IPTC_OBJECT_CYCLE = '075 ';
	const IPTC_BYLINE = '080 ';
	const IPTC_BYLINE_TITLE = '085 ';
	const IPTC_CITY = '090 ';
	const IPTC_PROVINCE_STATE = '095 ';
	const IPTC_COUNTRY_CODE = '100 ';
	const IPTC_COUNTRY = '101 ';
	const IPTC_ORIGINAL_TRANSMISSION_REFERENCE = '103 ';
	const IPTC_HEADLINE = '105 ';
	const IPTC_CREDIT = '110 ';
	const IPTC_SOURCE = '115 ';
	const IPTC_COPYRIGHT_STRING = '116 ';
	const IPTC_CAPTION = '120 ';
	const IPTC_LOCAL_CAPTION = '121 ';
	var $meta = [];
	var $hasmeta = false;
	var $file = false;

	function __construct( $filename ) {
		$size          = getimagesize( $filename, $info );
		$this->hasmeta = isset( $info["APP13"] );
		if ( $this->hasmeta ) {
			$this->meta = iptcparse( $info["APP13"] );
		}
		$this->file = $filename;
	}

	function set( $tag, $data ) {
		$this->meta ["2#$tag"] = [ $data ];
		$this->hasmeta         = true;
	}

	function get( $tag ) {
		return isset( $this->meta["2#$tag"] ) ? $this->meta["2#$tag"][0] : false;
	}

	function dump() {
		print_r( $this->meta );
	}

	function write() {
		if ( ! function_exists( 'iptcembed' ) ) {
			return false;
		}
		$mode     = 0;
		$content  = iptcembed( $this->binary(), $this->file, $mode );
		$filename = $this->file;

		@unlink( $filename ); #delete if exists

		$fp = fopen( $filename, "w" );
		fwrite( $fp, $content );
		fclose( $fp );
	}

	function binary() {
		$iptc_new = '';
		foreach ( array_keys( $this->meta ) as $s ) {
			$tag = str_replace( "2#", "", $s );
			$iptc_new .= $this->iptc_maketag( 2, $tag, $this->meta[ $s ][0] );
		}

		return $iptc_new;
	}

	function iptc_maketag( $rec, $dat, $val ) {
		$len = strlen( $val );
		if ( $len < 0x8000 ) {
			return chr( 0x1c ) . chr( $rec ) . chr( $dat ) .
			       chr( $len >> 8 ) .
			       chr( $len & 0xff ) .
			       $val;
		} else {
			return chr( 0x1c ) . chr( $rec ) . chr( $dat ) .
			       chr( 0x80 ) . chr( 0x04 ) .
			       chr( ( $len >> 24 ) & 0xff ) .
			       chr( ( $len >> 16 ) & 0xff ) .
			       chr( ( $len >> 8 ) & 0xff ) .
			       chr( ( $len ) & 0xff ) .
			       $val;
		}
	}

	#requires GD library installed

	function removeAllTags() {
		$this->hasmeta = false;
		$this->meta    = [];
		$img           = imagecreatefromstring( implode( file( $this->file ) ) );
		@unlink( $this->file ); #delete if exists
		imagejpeg( $img, $this->file, 100 );
	}

}

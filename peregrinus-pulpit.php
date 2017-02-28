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

/*
Plugin Name: PULPIT
Plugin URI: http://www.peregrinus.de/pulpit
Description: PULPIT is a sermon plugin for WordPress
Version: 1.0
Author: Christoph Fischer <chris@toph.de>
Author URI: http://christoph-fischer.org
License: GPL3
Text Domain: peregrinus-plugin
Domain Path: /Resources/Private/Languages
*/

error_reporting( E_ERROR );
ini_set( 'display_errors', 1 );

use Peregrinus\Pulpit\Pulpit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );

define( 'PEREGRINUS_PULPIT', 'pulpit' );
define( 'PEREGRINUS_PULPIT_START', time() );
define( 'PEREGRINUS_PULPIT_OPTIONS', PEREGRINUS_PULPIT . '_options' );
define( 'PEREGRINUS_PULPIT_SLUG', 'pulpit' );
define( 'PEREGRINUS_PULPIT_ENTRY_SCRIPT', __FILE__ );
define( 'PEREGRINUS_PULPIT_BASE_PATH', dirname( __FILE__ ) . '/' );
define( 'PEREGRINUS_PULPIT_CLASS_PATH', dirname( __FILE__ ) . '/Classes/' );
define( 'PEREGRINUS_PULPIT_BASE_URL',
	plugin_dir_url( PEREGRINUS_PULPIT_BASE_PATH ) . basename( dirname( __FILE__ ) ) . '/' );

add_action( 'plugins_loaded', [ Peregrinus\Pulpit\PulpitPlugin::class, 'getInstance' ], 9 );

if ( ! function_exists( '__dump' ) ) {
	function __dump( $v ) {
		die ( '<pre>' . print_r( $v, 1 ) );
	}
}

if ( ! function_exists( '__log' ) ) {
	function __log( $object, $text, $data = null ) {
		$reflect = new ReflectionClass( $object );
		$fp      = fopen( '/tmp/wp-cron.log', 'a' );
		fwrite( $fp,
			strftime( '%Y-%m-%d %H:%M:%S' ) .' ' .(time()-PEREGRINUS_PULPIT_START). ' ' . basename( $reflect->getShortName() ) . ' ' . $text .($data ? ' --> '.print_r($data, 1) : ''). PHP_EOL );
		fclose( $fp );
	}
}
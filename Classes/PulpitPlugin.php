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


namespace Peregrinus\Pulpit;

use Peregrinus\Pulpit\Admin\Admin;
use Peregrinus\Pulpit\Admin\Installer;
use Peregrinus\Pulpit\Admin\Scheduler;
use Peregrinus\Pulpit\PostTypes\PostTypeFactory;
use Peregrinus\Pulpit\Taxonomies\TaxonomyFactory;


/**
 * Class PulpitPlugin
 * Provides basic plugin registration
 * @package Peregrinus\Pulpit
 */
class PulpitPlugin {

	private static $instance = null;

	/**
	 * Pulpit constructor.
	 */
	public function __construct() {
		\register_activation_hook( PEREGRINUS_PULPIT_ENTRY_SCRIPT, [ Installer::class, 'activate' ] );
		\register_deactivation_hook( PEREGRINUS_PULPIT_ENTRY_SCRIPT, [ Installer::class, 'deactivate' ] );
		\register_uninstall_hook( PEREGRINUS_PULPIT_ENTRY_SCRIPT, [ Installer::class, 'uninstall' ] );

		\load_plugin_textdomain( 'pulpit', false,
			PEREGRINUS_PULPIT_DOMAIN_PATH );

		add_action( 'init', [ $this, 'init' ] );
		add_action( 'admin_init', [ Admin::class, 'init' ] );

		if (is_admin()) {
			$admin = new Admin();
			$admin->registerSettingsPages();
		}


	}

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return  \Peregrinus\Pulpit\Pulpit A single instance of this class.
	 */
	public
	static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin's registrations
	 */
	public function init() {
		foreach ( PostTypeFactory::getAll() as $postType ) {
			$postType->register();
		}

		foreach ( TaxonomyFactory::getAll() as $taxonomy ) {
			$taxonomy->register();
		}
		\flush_rewrite_rules();

		$scheduler = new Scheduler();
		$scheduler->register();
	}


}
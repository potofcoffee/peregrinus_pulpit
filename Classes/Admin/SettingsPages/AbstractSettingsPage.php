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


namespace Peregrinus\Pulpit\Admin\SettingsPages;


class AbstractSettingsPage {

	protected $options = [];
	protected $sections = [];
	protected $menuTitle = '';
	protected $pageTitle = '';
	protected $capability = 'manage_options';

	/**
	 * Start up
	 */
	public function __construct()
	{
	}

	/**
	 * Register all necessary hooks
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'addPluginPage' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Get the key for this SettingsPage
	 * @return string
	 */
	public function getKey() {
		return lcfirst( str_replace( 'SettingsPage', '', array_pop( explode( '\\', get_class( $this ) ) ) ) );
	}

	/**
	 * Get the slug for this SettingsPage
	 * @return mixed
	 */
	public function getSlug() {
		return PEREGRINUS_PULPIT.'-settings-'.$this->getKey();
	}


	/**
	 * Register the settings page
	 */
	public function addPluginPage() {
		add_options_page(
			$this->getPageTitle(),
			$this->getMenuTitle(),
			$this->getCapability(),
			$this->getSlug(),
			[$this, 'render']
		);
	}

	/**
	 * Configure all settings
	 */
	public function init() {
		register_setting(
			$this->getOptionGroupName(),
			$this->getOptionName(),
			[$this, 'sanitize']
		);
		foreach ($this->sections as $section) {
			$section->register($this);
		}
	}

	/**
	 * Get option group name
	 * @return string Option group name
	 */
	protected function getOptionGroupName() {
		return PEREGRINUS_PULPIT.'_options';
	}

	/**
	 * Get the option name for this SettingsPage
	 * @return string Option name
	 */
	protected function getOptionName() {
		return PEREGRINUS_PULPIT.'_'.$this->getKey();
	}

	/**
	 * Get a field's name
	 * @param $field Field name
	 */
	protected function getFieldName($field) {
		return $this->getOptionName().'['.$field.']';
	}


	protected function fetchOptions() {
		$this->options = get_option($this->getOptionName());
		return $this->options;
	}

	/**
	 * Render the settings page
	 */
	public function render() {
		$this->fetchOptions();
		echo '<div class="wrap">'
		     .'<h1>'.__('Settings').' > '.$this->getPageTitle().'</h1>'
		     .'<form method="post" action="options.php">';
		settings_fields($this->getOptionGroupName());
		do_settings_sections($this->getSlug());
		submit_button();
		echo '</form>'
		     .'</div>';
	}

	/**
	 * Sanitize input
	 * @param array $input Input
	 *
	 * @return array Sanitized input
	 */
	public function sanitize($input) {
		return $input;
	}

	/**
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions( $options ) {
		$this->options = $options;
	}

	/**
	 * @return string
	 */
	public function getMenuTitle() {
		return $this->menuTitle;
	}

	/**
	 * @param string $menuTitle
	 */
	public function setMenuTitle( $menuTitle ) {
		$this->menuTitle = $menuTitle;
	}

	/**
	 * @return string
	 */
	public function getPageTitle() {
		return $this->pageTitle;
	}

	/**
	 * @param string $pageTitle
	 */
	public function setPageTitle( $pageTitle ) {
		$this->pageTitle = $pageTitle;
	}

	/**
	 * @return string
	 */
	public function getCapability() {
		return $this->capability;
	}

	/**
	 * @param string $capability
	 */
	public function setCapability( $capability ) {
		$this->capability = $capability;
	}

	/**
	 * @return array
	 */
	public function getSections() {
		return $this->sections;
	}

	/**
	 * @param array $sections
	 */
	public function setSections( $sections ) {
		$this->sections = $sections;
	}



}
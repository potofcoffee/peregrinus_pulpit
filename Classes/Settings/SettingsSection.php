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


namespace Peregrinus\Pulpit\Settings;


use Peregrinus\Pulpit\Admin\SettingsPages\AbstractSettingsPage;

class SettingsSection {

	protected $settings = [];
	protected $id = '';
	protected $title = '';

	public function __construct($id, $title, $settings) {
		$this->setId(PEREGRINUS_PULPIT.'_section_'.$id);
		$this->setTitle($title);
		$this->setSettings($settings);
	}

	/**
	 * Add this SettingsSection to a page
	 * @param AbstractSettingsPage $page
	 */
	public function register(AbstractSettingsPage $page) {
		add_settings_section(
			$this->getId(),
			$this->getTitle(),
			[$this, 'render'],
			$page->getSlug()
		);
		foreach ($this->getSettings() as $setting) {
			$setting->register($page, $this);
		}
	}

	public function render() {
		echo 'This is section "'.$this->getTitle().'"';
	}

	/**
	 * @return array
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * @param array $settings
	 */
	public function setSettings( $settings ) {
		$this->settings = $settings;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle( $title ) {
		$this->title = $title;
	}


}
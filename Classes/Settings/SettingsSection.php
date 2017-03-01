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

/**
 * Class SettingsSection
 * @package Peregrinus\Pulpit\Settings
 */
class SettingsSection {

	protected $settings = [];
	protected $id = '';
	protected $title = '';
	protected $description = '';

	/**
	 * SettingsSection constructor.
	 *
	 * @param string $id Id
	 * @param string $title Title
	 * @param string $description Description text
	 * @param array $settings Settings
	 */
	public function __construct($id, $title, $description, $settings) {
		$this->setId(PEREGRINUS_PULPIT.'_section_'.$id);
		$this->setTitle($title);
		$this->setDescription($description);
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

	/**
	 * Render the description paragraph
	 */
	public function render() {
		echo '<p>'.$this->getDescription().'</p>';
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

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;
	}




}
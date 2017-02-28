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
use Peregrinus\Pulpit\Fields\AbstractField;

class Setting {
	protected $id = '';
	protected $label = '';
	protected $field = null;
	protected $page = null;

	public function __construct($id, $label, AbstractField $field) {
		$this->setId($id);
		$this->setLabel($label);
		$this->setField($field);
	}

	/**
	 * Register the setting
	 * @param AbstractSettingsPage $page SettingsPage
	 * @param SettingsSection $section SettingsSection
	 */
	public function register(AbstractSettingsPage $page, SettingsSection $section) {
		add_settings_field(
			$this->getId(),
			$this->getLabel(),
			[$this, 'render'],
			$page->getSlug(),
			$section->getId()
		);
		$this->setPage($page);
	}

	/**
	 * Render the setting field
	 */
	public function render() {
		if ($this->getPage()) {
			$options = $this->getPage()->getOptions();
		} else {
			$options = [];
		}
		echo $this->field->render($options);
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
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel( $label ) {
		$this->label = $label;
	}

	/**
	 * @return null
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 * @param null $field
	 */
	public function setField( $field ) {
		$this->field = $field;
	}

	/**
	 * @return null
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * @param null $page
	 */
	public function setPage( $page ) {
		$this->page = $page;
	}




}
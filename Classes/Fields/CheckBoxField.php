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


namespace Peregrinus\Pulpit\Fields;

/**
 * Class CheckBoxField
 * Provides configuration and rendering for a custom checkbox field
 * @package Peregrinus\Pulpit\Fields
 */
class CheckBoxField extends AbstractField {

	/**
	 * Output this field's form element
	 *
	 * @param array $value Custom field values
	 *
	 * @return string HTML output
	 */
	public function render($values) {
		return '<input type="checkbox" id="'.$this->getKey().'" name="'.$this->getFieldName().'" '.($this->getValue($values) ? ' checked': '').'>'.$this->renderLabel().'<br />';
	}


	/**
	 * Get this field's metadata from POST
	 * @return mixed Metadata value
	 */
	public function getValueFromPOST() {
		$value = parent::getValueFromPOST();
		return ($value ? true : false);
	}

}
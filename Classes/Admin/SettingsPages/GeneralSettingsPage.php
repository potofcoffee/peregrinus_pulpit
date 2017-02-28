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


use Peregrinus\Pulpit\Fields\InputField;
use Peregrinus\Pulpit\Settings\SettingsSection;
use Peregrinus\Pulpit\Settings\Setting;

class GeneralSettingsPage extends AbstractSettingsPage {

	public function __construct() {
		parent::__construct();
		$this->setPageTitle(__('Sermons', PEREGRINUS_PULPIT));
		$this->setMenuTitle(__('Sermons', PEREGRINUS_PULPIT));

		$this->setSections([
			new SettingsSection('sync', __('Synchronization', PEREGRINUS_PULPIT), [
				new Setting(
					'feed',
					__('Feed url', PEREGRINUS_PULPIT),
					new InputField('feed', '', $this->getOptionName())
					),
			]),
		]);
	}



}
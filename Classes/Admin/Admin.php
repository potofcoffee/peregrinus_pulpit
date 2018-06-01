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


namespace Peregrinus\Pulpit\Admin;


use Peregrinus\Pulpit\Admin\AdminMenus\AdminMenuFactory;
use Peregrinus\Pulpit\Admin\SettingsPages\SettingsPageFactory;
use Peregrinus\Pulpit\Admin\CustomModals\CustomModalFactory;
use Peregrinus\Pulpit\PostTypes\PostTypeFactory;

/**
 * Class Admin
 * Contains all basic admin-mode functions
 * @package Peregrinus\Pulpit\Admin
 */
class Admin {

	/**
	 * Initialize all functions for admin mode
	 */
	public function init() {

		// add meta boxes for all post types
		foreach ( PostTypeFactory::getAll() as $postType ) {
			$postType->addMetaBox();
			$postType->registerCustomColumns();
		}

		// register CustomModals
        foreach (CustomModalFactory::getAll() as $customModal) {
		    $customModal->register();
        }

		//foreach ( AdminMenuFactory::getAll() as $adminMenu) $adminMenu->adminInit();
	}

	/**
	 * Register AdminMenus
	 */
	public function registerAdminMenus() {
		foreach (AdminMenuFactory::getAll() as $adminMenu) $adminMenu->register();
	}

	/**
	 * Register SettingsPages
	 */
	public function registerSettingsPages() {
		foreach (SettingsPageFactory::getAll() as $settingsPage) $settingsPage->register();
	}

}
<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2018 Christoph Fischer, http://www.peregrinus.de
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

namespace Peregrinus\Pulpit\Admin\Setup;

use Peregrinus\Pulpit\Admin\Setup\Components\AbstractComponent;
use Peregrinus\Pulpit\Admin\Setup\Components\ComponentFactory;
use Peregrinus\Pulpit\Settings\SettingsSection;
use Peregrinus\Pulpit\Settings\SettingsTab;

class SetupSettingsSection extends SettingsSection
{
    public function __construct($id)
    {
        parent::__construct($id, '', '', []);
        wp_enqueue_script('pulpit-admin-setup',
            PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/ComponentInstaller.js');
    }

    public function register(SettingsTab $tab)
    {
        add_settings_section(
            $this->getId(),
            $this->getTitle(),
            [$this, 'render'],
            $tab->getSlug()
        );
    }

    public function render()
    {
        echo '<p></p>';
        echo '<h3>' . __('Components', 'pulpit') . '</h3>';

        /** @var AbstractComponent $component */
        foreach (ComponentFactory::getAll() as $component) {
            $class = $component->isInstalled() ? 'component-box-success' : 'component-box-warning';
            echo '<div class="component-box ' . $class . '">';
            echo '<b>' . $component->getTitle() . '</b><div class="spinner"></div><br />';
            echo $component->getDescription() . '<br />';
            if (!$component->isInstalled()) {
                echo '<a class="pulpit-component-install-button button" data-component="'
                    . $component->getKey()
                    . '">' . __('Install now', 'pulpit') . '</a>';
            }
            echo '</div>';
        }
        echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
    }

}

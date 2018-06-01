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


namespace Peregrinus\Pulpit\Admin\CustomModals;


class AbstractCustomModal
{


    /**
     * Register hooks
     */
    public function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueResources']);
        add_action('print_media_templates', [$this, 'includeTemplate']);
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueueResources()
    {
        wp_enqueue_script('custom-modal-' . $this->getKey(),
            PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/CustomModals/' . ucfirst($this->getKey()) . 'CustomModal.js');
    }

    /**
     * Get the key for this CustomModal
     * @return string
     */
    public function getKey()
    {
        $tmp = explode('\\', get_class($this));
        return lcfirst(str_replace('CustomModal', '', array_pop($tmp)));
    }

    public function includeTemplate($template)
    {
        $templateFile = PEREGRINUS_PULPIT_BASE_PATH . 'Resources/Private/Templates/Admin/CustomModals/' . ucfirst($this->getKey()) . '.html';
        echo '<!-- trying to load CustomModal template ' . $templateFile . ' -->';
        if (file_exists($templateFile)) {
            echo file_get_contents($templateFile);
        }
    }
}

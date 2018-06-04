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

namespace Peregrinus\Pulpit\CustomFormats;

class AbstractCustomFormat
{

    protected $defaultExtension = 'html';

    /**
     * Register the hook for this CustomFormat
     */
    public function register()
    {
        \add_action('wp', [$this, 'run']);
    }

    /**
     * Check whether this CustomFormat should be output
     */
    public function run()
    {
        if ($_GET['format'] == PEREGRINUS_PULPIT . '_' . $this->getKey()) {
            $this->render();
        }
    }

    /**
     * Get the key for this CustomFormat
     * @return string
     */
    public function getKey()
    {
        $tmp = explode('\\', get_class($this));
        return lcfirst(str_replace('CustomFormat', '', array_pop($tmp)));
    }

    /**
     * Render the CustomFormat.
     * This function must be overridden by any CustomFormat class.
     */
    public function render()
    {
    }

    protected function getViewFilePath()
    {
        $fileName = PEREGRINUS_PULPIT_BASE_PATH . 'Resources/Private/Templates/CustomView/' . ucfirst($this->getKey());
        return file_exists($fileName . '.php') ? $fileName . '.php' : $fileName . '.'.$this->defaultExtension;
    }
}

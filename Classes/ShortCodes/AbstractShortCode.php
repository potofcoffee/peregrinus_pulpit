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

namespace Peregrinus\Pulpit\ShortCodes;

use Peregrinus\Pulpit\Utility\StringUtility;
use Peregrinus\Pulpit\View;

abstract class AbstractShortCode
{

    /** @var View view  */
    protected $view = null;

    public function __construct() {
        $this->view = new View();
        $this->view->getRenderingContext()->setControllerName('ShortCodes');
    }

    /**
     * Register the ShortCode
     *
     * The shortcode will be derrived from an underscored version of the class name, prefixed by "pulpit_"
     * e.g. the shortcode for Peregrinus\Pulpit\ShortCodes\MyFirstExampleShortCode will be
     * pulpit_my_first_example.
     */
    public function register()
    {
        $shortCode = PEREGRINUS_PULPIT.'_'.StringUtility::CamelCaseToUnderscore($this->getKey());

        add_shortcode($shortCode, [$this, 'render']);
    }


    /**
     * Get the key for this PostType
     * @return string
     */
    public function getKey()
    {
        $tmp = explode('\\', get_class($this));
        return lcfirst(str_replace('ShortCode', '', array_pop($tmp)));
    }

    public function render() {
        $this->prepareView();
        return $this->renderView();
    }

    /**
     * Render the shortcode view.
     * Override this function to output directly from php instead of rendering a view
     * @return string Rendered output
     */
    public function renderView() {
        return $this->view->render(ucfirst($this->getKey()));
    }

    /**
     * Prepare the necessary data for the view
     * This function needs to be overridden in all descendant classes.
     * @return void
     */
    abstract public function prepareView();
}

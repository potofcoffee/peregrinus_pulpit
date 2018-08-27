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

namespace Peregrinus\Pulpit;

use TYPO3Fluid\Fluid\View\TemplateView;

/**
 * Class View
 * Extends the Fluid's TemplateView and sets default paths for this plugin
 * @package Peregrinus\Pulpit
 */
class View extends TemplateView
{

    /**
     * View constructor.
     * Call TemplateView constructor and set default paths
     *
     * @param string prefix Path prefix to use
     * @param null $context
     */
    public function __construct($prefix = '', $context = null)
    {
        parent::__construct($context);
        $paths = $this->getTemplatePaths();
        $paths->setTemplateRootPaths([PEREGRINUS_PULPIT_BASE_PATH . 'Resources/Private/Templates/' . $prefix]);
        $paths->setLayoutRootPaths([PEREGRINUS_PULPIT_BASE_PATH . 'Resources/Private/Layouts/']);
        $paths->setPartialRootPaths([PEREGRINUS_PULPIT_BASE_PATH . 'Resources/Private/Partials/']);

        $this->getRenderingContext()->getViewHelperResolver()->addNamespaces([
                'wp' => 'Peregrinus\\Pulpit\\ViewHelpers\\WordPress',
                'p' => 'Peregrinus\\Pulpit\\ViewHelpers\\Pulpit',
            ]
        );
        $this->assign('plugin_slug', PEREGRINUS_PULPIT);
        $this->assign('basePath', PEREGRINUS_PULPIT_BASE_PATH);
        $this->assign('baseUrl', PEREGRINUS_PULPIT_BASE_URL);
    }

    public function render($actionName = null, $controllerName = null)
    {
        if ($controllerName) $this->getRenderingContext()->setControllerName($controllerName);
        return parent::render($actionName);
    }

}

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

namespace Peregrinus\Pulpit;

use Peregrinus\Pulpit\Controllers\AbstractController;

class FrontendDispatcher
{

    /** @var FrontendDispatcher */
    protected static $instance = null;

    /** @var AbstractController */
    protected $controller = null;

    /** @var string action */
    protected $action = '';

    /** @var string post_type */
    protected $postType = '';

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function themeTemplateExists()
    {
        return file_exists($this->getThemeTemplate());
    }

    public function getThemeTemplate()
    {
        return get_template_directory() . '/' . $this->action . '-' . $this->getPostType() . '.php';
    }

    protected function getControllerClass()
    {
        return 'Peregrinus\\Pulpit\\Controllers\\' . $this->getKey() . 'Controller';
    }

    protected function getKey()
    {
        return ucfirst(str_replace(PEREGRINUS_PULPIT . '_', '', $this->postType));
    }

    /**
     * Load the appropriate template for this request
     *
     * This will respect the following template hierarchy
     * - If a theme template is present, it will take precedence
     * - Else: If a controller for this post type is present, it will be loaded through frontend.php
     * - Else: If a php template for this post_type is present, it will be used
     * - Else: Previous WP template resolution will remain unchanged
     *
     * @param $post
     * @param $template
     * @return string
     */
    public function resolveTemplate($post, $template)
    {
        // get action
        if (is_singular()) {
            $action = $_REQUEST['format'] ?: 'single';
        } elseif (is_archive()) {
            $action = 'archive';
        } else {
        }

        $this->setAction($action);
        if ($this->themeTemplateExists()) {
            $template = $this->getThemeTemplate();
        } else {
            if (class_exists($controllerClass = $this->getControllerClass())) {
                $this->setController(new $controllerClass());
                $template = PEREGRINUS_PULPIT_BASE_PATH . 'frontend.php';
            } else {
                foreach ([
                             PEREGRINUS_PULPIT_BASE_PATH . 'Resources/Private/Templates/PostTypes/' . $this->getKey() . '.php',
                             PEREGRINUS_PULPIT_BASE_PATH . $this->action . '-' . $this->getPostType() . '.php'
                         ]
                         as $templateFile) {
                    if (file_exists($templateFile)) {
                        $template = $templateFile;
                        continue;
                    }
                }
            }
        }

        return $template;
    }

    public function render() {
        return $this->getController()->render($this->action);
    }

    /**
     * @return AbstractController
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param AbstractController $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getPostType()
    {
        return $this->postType;
    }

    /**
     * @param string $postType
     */
    public function setPostType($postType)
    {
        $this->postType = $postType;
    }

}

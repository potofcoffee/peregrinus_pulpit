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

namespace Peregrinus\Pulpit\Controllers;

use Peregrinus\Pulpit\Fluid\DynamicVariableProvider;
use Peregrinus\Pulpit\View;

class AbstractController
{

    /** @var View  */
    protected $view = null;
    protected $prefix = 'PostTypes/';

    protected $action = '';

    public function __construct()
    {
        $this->view = new View($this->prefix);
        $this->view->getRenderingContext()->setVariableProvider(new DynamicVariableProvider());
        $this->view->assign('baseUrl', PEREGRINUS_PULPIT_BASE_URL);
    }

    /**
     * Get the key for this Controller
     * @return string
     */
    public function getKey()
    {
        $tmp = explode('\\', get_class($this));
        return lcfirst(str_replace('Controller', '', array_pop($tmp)));
    }


    public function render($action) {
        $this->setAction($action);
        $actionMethod = $this->action.'Action';
        if (method_exists($this, $actionMethod)) {
            $queriedObject = get_queried_object();
            if (is_a($queriedObject, \WP_Post::class)) {
                $this->$actionMethod($this->transformQueryObject($queriedObject));
            } else {
                $this->$actionMethod();
            }
        }
        return $this->view->render($this->action, ucfirst($this->getKey()));
    }

    protected function transformQueryObject($queryObject) {
        return $queryObject;
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


}

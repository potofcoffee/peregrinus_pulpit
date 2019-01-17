<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2019 Christoph Fischer, http://www.peregrinus.de
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

namespace Peregrinus\Pulpit\Admin\DashboardPanels;

use Peregrinus\Pulpit\AbstractObject;
use Peregrinus\Pulpit\Utility\StringUtility;

class AbstractDashboardPanel extends AbstractObject
{

    /** @var string $title Title */
    protected $title;

    public function __construct()
    {
    }

    public function register() {
        wp_add_dashboard_widget(
            PEREGRINUS_PULPIT.'_'.StringUtility::CamelCaseToUnderscore($this->getKey()),
            $this->getTitle(),
            [$this, 'render']
        );
    }


    public function render() {
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }



}

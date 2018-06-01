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


namespace Peregrinus\Pulpit\ViewHelpers\WordPress;


use Peregrinus\Pulpit\ViewHelpers\AbstractBufferedViewHelper;

/**
 * Class TranslateViewHelper
 * Exposes WordPress' settings_fields to Fluid
 * @package Peregrinus\Pulpit\ViewHelpers
 */
class SettingsFieldsViewHelper extends AbstractBufferedViewHelper
{

    /**
     * @var boolean
     */
    protected $escapeChildren = false;
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('group', 'string', 'Option group', false, PEREGRINUS_PULPIT . '_options');
    }

    /**
     * Render the output using settings_fields
     */
    protected function renderBuffered()
    {
        settings_fields($this->arguments['group']);
    }

}
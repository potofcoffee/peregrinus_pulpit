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

namespace Peregrinus\Pulpit\Admin\Migrations;

use Peregrinus\Pulpit\Debugger;

/**
 * Class EGTextMigration
 * @package Peregrinus\Pulpit\Admin\Migrations
 *
 * This migration does the following:
 * - Import EG song texts from temporary EG.raw.yaml
 */
class EGTextMigration extends AbstractMigration
{
    protected $title = 'EG full text';
    protected $description = 'Add the full text to (most) EG songs';

    public function execute()
    {
        echo '<h1>EGTExtMigration</h1>';

        $eg = yaml_parse_file(PEREGRINUS_PULPIT_BASE_PATH.'Assets/EG/EG.yaml');
        $egText = yaml_parse_file(PEREGRINUS_PULPIT_BASE_PATH.'Assets/Songbooks/EG.raw.yaml');

        foreach ($eg as $no => $song) {
            $eg[$no] = array_merge(['title' => $song], $egText[$no] ?: []);
        }
        yaml_emit_file(PEREGRINUS_PULPIT_BASE_PATH.'Assets/Songbooks/EG.yaml', $eg);
    }

}

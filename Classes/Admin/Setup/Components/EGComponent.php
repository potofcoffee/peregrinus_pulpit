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

namespace Peregrinus\Pulpit\Admin\Setup\Components;

class EGComponent extends AbstractComponent
{
    protected $title = 'Evangelisches Gesangbuch';
    protected $description = 'Das Verzeichnis zum Evangelischen Gesangbuch (inkl. Texte und MIDI-Dateien) kann von <a href="http://www.l4a.org/l4a/4bibeln/index.html" target="_blank">4Bibeln</a> automatisch heruntergeladen werden.';

    public function isInstalled(): bool
    {
        return file_exists(PEREGRINUS_PULPIT_BASE_PATH.'Assets/EG/EG.yaml');
    }


    public function install() {
        __dump('hi');
        echo json_encode([
            'success' => false,
            'notice' => 'Not implemented.',
        ]);
    }

}

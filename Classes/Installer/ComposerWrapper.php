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


namespace Peregrinus\Pulpit\Installer;


class ComposerWrapper
{

    const COMPOSER_DOWNLOAD_URL = 'https://getcomposer.org/composer.phar';

    /** @var string Command to run composer */
    protected $composerCmd = '';

    public function __construct()
    {
        $this->getComposer();
    }

    /**
     * Find the path to composer
     * This will download composer.phar locally, if no global composer install is found
     */
    protected function getComposer() {
        $composerPath = trim(shell_exec('which composer'));
        if ($composerPath) {
            $this->composerCmd = $composerPath;
        } else {
            if (!file_exists(PEREGRINUS_PULPIT_BASE_PATH.'composer.phar')) {
                file_put_contents(PEREGRINUS_PULPIT_BASE_PATH.'composer.phar', file_get_contents(self::COMPOSER_DOWNLOAD_URL));
            }
            $this->composerCmd = 'php composer.phar';
        }
    }

    /**
     * @return bool True if composer is present
     */
    public function hasComposer(): bool {
        return ($this->composerCmd != '');
    }

    /**
     * Execute composer with given command line
     * @param $commandLine Command line
     * @return string Output
     */
    public function do($commandLine) {
        chdir(PEREGRINUS_PULPIT_BASE_PATH);
        return shell_exec('HOME='.$_SERVER['DOCUMENT_ROOT'].' '.$this->composerCmd.' '.$commandLine.' 2>&1');
    }

    /**
     * Check for outdated packages on this install
     * @return bool True if outdated packages exist
     */
    public function isOutdated(): bool {
        return (array_sum($this->getTaskStats()) > 0);
    }

    /**
     * Check whether initial install has been run
     * At the moment, this simply checks whether composer.lock exists
     * @return bool True if install has been run
     */
    public function isInstalled(): bool {
        return (file_exists(PEREGRINUS_PULPIT_BASE_PATH.'composer.lock'));
    }

    /**
     * Run the initial installer
     */
    public function install() {
        $this->do('install --no-interaction');
    }

    /**
     * Run all outstanding updates
     */
    public function update() {
        $this->do('update --no-interaction');
    }

    /**
     * Get the number of installs/updates/removals needed
     * @return array Stats for installs/updates/removals needed
     */
    public function getTaskStats() {
        preg_match(
            '/(\d+) installs, (\d+) updates, (\d+) removals/',
            $this->do('update --dry-run'),
            $stats
        );
        if (count($stats)) unset($stats[0]); else return [1 => 0, 2=> 0, 3=> 0];
        return ['install' => $stats[1], 'update' => $stats[2], 'remove' => $stats[3]];
    }
}
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

namespace Peregrinus\Pulpit\Admin\ExternalPlayers;

class AbstractExternalPlayer
{
    protected $curl;

    /**
     * Register this player to any filters, hooks, etc.
     * This is called during plugin init
     */
    public function register() {}

    /**
     * Check if this player is sufficiently configured to be usable
     * @return bool True if player is usable
     */
    public function isEnabled(): bool {
        return false;
    }

    /**
     * Register settings fields for this player
     * These settings will appear under the "podcast" section in the general settings page
     * @return array SettingsFields
     */
    public function registerSettings(): array {
        return [];
    }


    /**
     * Find the corresponding episode for a sermon
     * @param SermonModel $sermon Sermon
     * @return array|bool Player data, false if none was found
     */
    public function getPlayerData(SermonModel $sermon) {
        return [];
    }


    /**
     * Find the corresponding episode for a sermon
     * @param SermonModel $sermon Sermon
     * @return array|bool Raw episode data, false if none was found
     */
    public function findEpisode(SermonModel $sermon) {
        return false;
    }


    protected function setArgumentsFromArray($option, $data)
    {
        $arguments = [];
        foreach ($data as $key => $val) {
            $arguments[] = $key . '=' . $val;
        }
        if (count($arguments)) {
            curl_setopt($this->curl, $option, join('&', $arguments));
        }
    }

    public function post($url, array $curlArguments = [], array $headers = [], array $params = [])
    {
        $curlArguments[CURLOPT_POST] = 1;
        return $this->exec($url, $curlArguments, $headers, $params);
    }

    public function get($url, array $curlArguments = [], array $headers = [], array $params = [])
    {
        return $this->exec($url, $curlArguments, $headers, $params);
    }

    protected function exec($url, array $curlArguments = [], array $headers = [], array $params = [])
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        foreach ($curlArguments as $option => $argument) {
            curl_setopt($this->curl, $option, $argument);
        }
        if (count($headers)) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        }
        $this->setArgumentsFromArray(CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($this->curl);
        curl_close($this->curl);
        return json_decode($result, true);
    }

    public function getKey()
    {
        $tmp = explode('\\', get_class($this));
        return strtolower(str_replace('ExternalPlayer', '', array_pop($tmp)));
    }


}

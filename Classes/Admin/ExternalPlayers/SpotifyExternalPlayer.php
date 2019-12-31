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

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\SermonModel;
use Peregrinus\Pulpit\Fields\InputField;
use Peregrinus\Pulpit\Settings\Setting;

class SpotifyExternalPlayer extends AbstractExternalPlayer
{

    /** @var string $accessToken */
    protected $accessToken;

    /** @var string $clientId */
    protected $clientId = '';

    /** @var string $clientSecret */
    protected $clientSecret = '';


    public function __construct()
    {
        $this->clientId = get_option('pulpit_spotify_client_id');
        $this->clientSecret = get_option('pulpit_spotify_client_secret');
    }

    /**
     * Check if this player is sufficiently configured to be usable
     * @return bool True if player is usable
     */
    public function isEnabled(): bool {
        return ($this->clientId != '') && ($this->clientSecret != '') && ($this->getShowUrl() != '');
    }

    public function registerSettings(): array
    {
        return [
            new Setting(
                'spotify_show_url',
                __('Spotify Show Url', 'pulpit'),
                new InputField('pulpit_spotify_show_url', '')
            ),
            new Setting(
                'spotify_client id',
                __('Spotify API Client ID', 'pulpit'),
                new InputField('pulpit_spotify_client_id', '')
            ),
            new Setting(
                'spotify_client secret',
                __('Spotify API Client secret', 'pulpit'),
                new InputField('pulpit_spotify_client_secret', '')
            ),
        ];
    }

    /**
     * Register this player with the SermonModel's filters
     * This will provide an additional getSpotify() getter to the SermonModel
     */
    public function register() {
        add_filter('pulpit_sermon_getters', [$this, 'getSpotifyGetter']);
    }

    public function getSpotifyGetter($getters) {
        $getters[] = [$this, 'getSpotify'];
        return $getters;
    }

    public function getSpotify(SermonModel $sermon) {
        return $this->findEpisode($sermon);
    }

    public function getPlayerData(SermonModel $sermon) {
        $episode = $this->findEpisode($sermon);
        $episodeData = [
            'url' => $episode['external_urls']['spotify'],
            'key' => $this->getKey(),
            'badge' => PEREGRINUS_PULPIT_BASE_URL.'Resources/Public/Images/Badges/spotify.png',
            'show' => get_option('pulpit_spotify_show_url'),
        ];
        return $episodeData;
    }

    /**
     * Get an access token from Spotify
     * This will set the $accessToken property
     */
    public function authorize()
    {
        $result = $this->post('https://accounts.spotify.com/api/token', [],
            ['Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)],
            ['grant_type' => 'client_credentials']
        );
        $this->accessToken = $result['access_token'];
    }

    /**
     * Make an api call with Authorization headers passed
     * @param $url Api url
     * @param string $type Request type (GET/POST)
     * @return array Data
     */
    public function authorizedApiCall($url, $type='GET') {
        if (!$this->accessToken) $this->authorize();
        $callFunc = method_exists($this, strtolower($type)) ? strtolower($type) : 'get';
        $headers = ['Authorization: Bearer ' . $this->accessToken];
        return $this->$callFunc($url, [], $headers, []);
    }

    /**
     * Find the corresponding episode for a sermon on spotify
     * @param SermonModel $sermon Sermon
     * @return array|bool Episode data, false if none was found
     */
    public function findEpisode(SermonModel $sermon) {
        $url = $this->getShowUrl();
        if ($url) {
            $data = $this->authorizedApiCall($url);
            foreach ($data['episodes']['items'] as $episode) {
                if (($episode['name'] == $sermon->getTitle())) {
                    return $episode;
                }
            }
        }
        return false;
    }

    /**
     * Get the url of the show on spotify
     * @return string Url
     */
    public function getShowUrl() {
        $urlParts = parse_url(trim(get_option('pulpit_spotify_show_url')));
        return 'https://api.spotify.com/v1/shows/'.pathinfo($urlParts['path'], PATHINFO_FILENAME);
    }


}

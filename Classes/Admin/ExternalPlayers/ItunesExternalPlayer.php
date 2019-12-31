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
use Symfony\Component\Debug\Debug;

class ItunesExternalPlayer extends AbstractExternalPlayer
{

    /**
     * Check if this player is sufficiently configured to be usable
     * @return bool True if player is usable
     */
    public function isEnabled(): bool {
        return ($this->getShowUrl() != '');
    }

    public function registerSettings(): array
    {
        return [
            new Setting(
                'itunes_show_url',
                __('Itunes Show Url', 'pulpit'),
                new InputField('pulpit_itunes_show_url', '')
            ),
        ];
    }

    /**
     * Register this player with the SermonModel's filters
     * This will provide an additional getITunes() getter to the SermonModel
     */
    public function register() {
        add_filter('pulpit_sermon_getters', [$this, 'getITunesGetter']);
    }

    public function getITunesGetter($getters) {
        $getters[] = [$this, 'getITunes'];
        return $getters;
    }

    public function getITunes(SermonModel $sermon) {
        return $this->findEpisode($sermon);
    }

    public function getPlayerData(SermonModel $sermon) {
        $episode = $this->findEpisode($sermon);
        if (!$episode) return [];
        $episodeData = [
            'url' => $episode,
            'key' => $this->getKey(),
            'badge' => PEREGRINUS_PULPIT_BASE_URL.'Resources/Public/Images/Badges/itunes.png',
            'show' => $this->getShowUrl(),
        ];
        return $episodeData;
    }

    /**
     * Find the corresponding episode for a sermon on spotify
     * @param SermonModel $sermon Sermon
     * @return string|bool Episode url, false if none was found
     */
    public function findEpisode(SermonModel $sermon) {
        // cached?
        $meta = get_post_meta($sermon->getID(), $this->getKey());
        // if ($meta) return $meta;

        $showId = $this->getShowId();
        $showData = $this->get('http://itunes.apple.com/lookup?id='.$showId);
        if ($showData['resultCount']>0) {
            $showData = $showData['results'][0];
            $src = \phpQuery::newDocumentFileHTML($showData['trackViewUrl']);
            $tr = pq('table.tracklist-table tr td.name');
            foreach ($tr as $thisRow) {
                if (pq($thisRow)->attr('sort-value') == $sermon->getTitle()) {
                    preg_match('/\'(.*?)\'/', pq($thisRow)->parent()->find('td.view-in-itunes a')->attr('onclick'), $tmp);
                    // cache in post meta
                    update_post_meta($sermon->getID(), $this->getKey(), $tmp[1]);
                    return $tmp[1];
                };
            }
        }
        return false;
    }

    /**
     * Get the url of the show on spotify
     * @return string Url
     */
    public function getShowUrl() {
        return get_option('pulpit_itunes_show_url');
    }

    protected function getShowId() {
        return str_replace('id', '', pathinfo($this->getShowUrl(), PATHINFO_FILENAME));
    }


}

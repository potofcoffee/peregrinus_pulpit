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

namespace Peregrinus\Pulpit\Domain\Model;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Repository\AttachmentRepository;
use Peregrinus\Pulpit\Domain\Repository\EventRepository;

class SermonModel extends AbstractModel
{
    public function __construct(\WP_Post $post)
    {
        parent::__construct($post);
        $taxonomies = get_object_taxonomies($post);
        foreach ($taxonomies as $taxonomy) {
            $terms = wp_get_post_terms($post->ID, $taxonomy);
            if (substr($taxonomy, 0, strlen(PEREGRINUS_PULPIT) + 1) == PEREGRINUS_PULPIT . '_') {
                $taxonomy = explode('_', $taxonomy)[2];
            }
            $this->setMetaElement(str_replace(PEREGRINUS_PULPIT . '_', '', $taxonomy), $terms);
        }
    }

    public function addEvent($event) {
        if (is_numeric($event)) $event = (new EventRepository())->findByID($event);
        $this->meta['events'][$event->ID] = $event;
    }

    public function removeEvent($event) {
        if (is_numeric($event)) $event = (new EventRepository())->findByID($event);
        unset($this->meta['events'][$event->ID]);
    }

    public function setEventsMeta($event)
    {
        $events = $this->meta['events'] ?: get_post_meta($this->post->ID, 'events');
        if (is_array($event)) {
            $events = $event;
            foreach ($events as $key => $event) {
                $events[$key] = (new EventRepository())->findByID($event);
            }
        } elseif ($event > 0) {
            $events = [(new EventRepository())->findByID($event)];
        }
        $this->setMetaElement('events', $events);
    }

    protected function setAudiorecordingRelationMeta($id)
    {
        if ($id > 0) {
            $this->setMetaElement('audiorecording_relation',
                (new AttachmentRepository())->findByID($id)
            );
        }
    }

    protected function getSermonSeries()
    {
        return get_the_terms($this->post->ID, PEREGRINUS_PULPIT . '_sermon_series');
    }

}

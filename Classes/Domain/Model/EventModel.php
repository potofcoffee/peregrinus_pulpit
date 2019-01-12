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

use Peregrinus\Pulpit\AgendaItems\AbstractAgendaItem;
use Peregrinus\Pulpit\AgendaItems\AgendaItemFactory;
use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Repository\LocationRepository;

class EventModel extends AbstractModel
{

    protected $eventTime = 0;

    public function getFormattedDateTime($format)
    {
        if (!$this->eventTime) {
            $this->eventTime = strtotime($this->getDate() . ' ' . $this->getTime());
        }
        return strftime($format, $this->eventTime);
    }

    protected function setLocationMeta($location)
    {
        if ($location > 0) {
            $location = (new LocationRepository())->findByID($location);
        }
        $this->setMetaElement('location', $location);
    }

    protected function setLiturgyMeta($liturgy) {
        if (is_array($liturgy)) {
            foreach ($liturgy as $key => $item) {
                $item = maybe_unserialize($item);
                /** @var AbstractAgendaItem $itemObject */
                $itemObject = AgendaItemFactory::get($item['type']);
                if ($itemObject === null) Debugger::dumpAndDie([$item['type'], $item, $liturgy, $this]);
                $item['data'] = $itemObject->provideData($item['data']);
                $liturgy[$key] = $item;
            }
        }
        $this->setMetaElement('liturgy', $liturgy);
    }

    /**
     * @return int
     */
    public function getEventTime()
    {
        return $this->eventTime;
    }

    /**
     * @param int $eventTime
     */
    public function setEventTime($eventTime)
    {
        $this->eventTime = $eventTime;
    }

    public function getDateTime() {
        return new \DateTime($this->meta['date'].' '.$this->meta['time']);
    }


}

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

namespace Peregrinus\Pulpit\Controllers;

use Peregrinus\Pulpit\Domain\Model\EventModel;
use Peregrinus\Pulpit\Domain\Repository\EventRepository;
use Peregrinus\Pulpit\Domain\Repository\SermonRepository;

class EventController extends AbstractController
{

    /** @var EventRepository */
    protected $eventRepository = null;

    /** @var SermonRepository */
    protected $sermonRepository = null;

    public function __construct()
    {
        parent::__construct();
        $this->eventRepository = new EventRepository();
        $this->sermonRepository = new SermonRepository();
        $this->sermonRepository->setIncludepostStatus(['publish', 'future']);
    }

    public function archiveAction()
    {
        $events = $this->eventRepository->get([
            'meta_key' => 'date',
            'orderby' => 'meta_value',
            'order' => 'asc',
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'date',
                    'value' => date('Y-m-d'),
                    'compare' => '>',
                    'type' => 'DATE'
                ]
            ]
        ]);
        /**
         * @var  $key
         * @var EventModel $event
         */
        foreach ($events as $key => $event) {
            $sermon = $this->sermonRepository->findOneByEventID($event->getID());
            if ($sermon) {
                $event->setMetaElement('sermon', $sermon);
            }
        }
        $this->view->assign('events', $events);
    }

    private function getEventDataFromPost($post)
    {
        if (!$post) {
            $post = get_queried_object();
        }
        $event = new EventModel($post);
        $sermon = $this->sermonRepository->findOneByEventID($event->getID());
        if ($sermon) {
            $event->setMetaElement('sermon', $sermon);
        }
        $this->view->assign('event', $event);
        return $event;
    }

    public function singleAction($post)
    {
        $event = $this->getEventDataFromPost($post);
        $this->view->assign('event', $event);
    }

    public function liturgyAction($post)
    {
        $event = $this->getEventDataFromPost($post);
        $this->view->assign('event', $event);
    }

    public function preacherNotesAction($post)
    {
        $event = $this->getEventDataFromPost($post);
        $this->view->assign('event', $event);
    }

}

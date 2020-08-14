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

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\EventModel;
use Peregrinus\Pulpit\Domain\Repository\EventRepository;
use Peregrinus\Pulpit\Domain\Repository\SermonRepository;

/**
 * Class EventController
 * @package Peregrinus\Pulpit\Controllers
 */
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

    /**
     * Show a list of upcoming events
     */
    public function archiveAction()
    {
        $ppEvents = json_decode($this->getUrl('https://www.pfarrplaner.de/api/user/1/services'), true)['services'];

        $events = $this->eventRepository->get([
            'meta_key' => 'date',
            'orderby' => 'meta_value',
            'order' => 'asc',
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'date',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE'
                ]
            ]
        ]);


        $finalEvents = [];

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
        $finalEvents[$event->getDateTime()->format('YmdHi')] = $event;



        foreach ($ppEvents as $ppEvent) {
            if ((count($ppEvent['funerals']) == 0) && (count($ppEvent['weddings']) == 0)) {
                $dt = new \DateTime(substr($ppEvent['day']['date'],0, 10).' '.$ppEvent['time']);
                if (!isset($finalEvents[$dt->format('YmdHi')])) {
                    $finalEvents[$dt->format('YmdHi')] = $ppEvent;
                };
            }
        }

        ksort ($finalEvents);
        $this->view->assign('events', $finalEvents);
    }

    /**
     * Get the event data model from the requested WP_Post object
     * @param \WP_Post $post Requested Post
     * @return EventModel Event data model
     */
    private function getEventDataFromPost(\WP_Post $post)
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

    /**
     * Show the details view for the requested post
     * @param \WP_Post $post Requested post
     */
    public function singleAction(\WP_Post $post)
    {
        $event = $this->getEventDataFromPost($post);
        $this->view->assign('event', $event);
    }

    /**
     * Create a liturgy sheet for the requested post
     * @param \WP_Post $post Requested post
     */
    public function liturgyAction(\WP_Post $post)
    {
        $event = $this->getEventDataFromPost($post);
        $this->view->assign('event', $event);
    }

    /**
     * Create preacher's notes for the requested post
     * @param \WP_Post $post Requested post
     */
    public function preacherNotesAction(\WP_Post $post)
    {
        $event = $this->getEventDataFromPost($post);
        $this->view->assign('event', $event);
    }

    /**
     * Create songsheet for the requested post
     * @param \WP_Post $post Requested post
     */
    public function songsheetAction(\WP_Post $post)
    {
        $event = $this->getEventDataFromPost($post);
        $this->view->assign('event', $event);
    }

    /**
     * Create a large-scale song list for the requested post
     * @param \WP_Post $post Requested post
     */
    public function publicSongListAction(\WP_Post $post)
    {
        $event = $this->getEventDataFromPost($post);
        $this->view->assign('event', $event);
    }

    /**
     * Create a link label for the requested post
     * @param \WP_Post $post Requested post
     */
    public function linkLabelAction(\WP_Post $post)
    {
        $event = $this->getEventDataFromPost($post);
        $this->view->assign('event', $event);
    }


    protected function setAppView(string $appAction) {
        $this->action = 'app/'.ucfirst($appAction);
    }


    public function appAction(\WP_Post $post) {
        $event = $this->getEventDataFromPost($post);

        $appSection = filter_var($_GET['app'], FILTER_SANITIZE_STRING) ?: 'liturgy';
        $this->setAppView($appSection);

        // get a hierarchical array for liturgy
        $liturgy = [];
        $currentLiturgySection = '';
        foreach ($event->getLiturgy() as $item) {
            if ($item['type'] == 'sectionTitle') {
                $currentLiturgySection = $item['title'];
                $liturgy[$currentLiturgySection]['heading'] = $item;
            } else {
                $liturgy[$currentLiturgySection]['items'][] = $item;
            }
        }

        $this->view->assign('event', $event);
        $this->view->assign('liturgy', $liturgy);
    }


    protected function getUrl(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}

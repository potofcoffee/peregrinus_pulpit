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

namespace Peregrinus\Pulpit\Admin\DashboardPanels;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\EventModel;
use Peregrinus\Pulpit\Domain\Repository\EventRepository;
use Peregrinus\Pulpit\Domain\Repository\SermonRepository;

class EventsDashboardPanel extends AbstractDashboardPanel
{
    /** @var EventRepository $eventRepository */
    protected $eventRepository = null;

    /** @var SermonRepository $sermonRepository */
    protected $sermonRepository = null;

    public function __construct()
    {
        $this->setTitle(__('Church services', 'pulpit'));
        $this->eventRepository = new EventRepository();
        $this->eventRepository->setIncludepostStatus(['publish', 'future', 'draft', 'private', 'pulpit_hidden']);
        $this->sermonRepository = new SermonRepository();
        $this->sermonRepository->setIncludepostStatus(['publish', 'future', 'draft', 'private', 'pulpit_hidden']);
    }


    protected function renderList($events, $title)
    {
        /** @var EventModel $event */

        echo '<h3>' . $title . '</h3>';

        echo '<ul>';
        foreach ($events as $event) {
            $sermon = $this->sermonRepository->findOneByEventID($event->getID());
            echo '<li>'
                . '<span>' . $event->getDateTime()->format(__('d.m.Y, H:i', 'pulpit')) . '</span> '
                . $event->getTitle();
            if ($sermon)
                echo '<br />' . __('Sermon', 'pulpit') . ': '
                    . $sermon->getTitle();
            echo '<br />'
                . '<a class="button button-small" href="'
                . get_edit_post_link($event->getID()) . '">'
                . __('Edit event', 'pulpit')
                . '</a>'
                . ' <a class="button button-small" target="_blank" href="'
                . get_permalink($event->getID()) . '">'.__('Event page', 'pulpit').'</a>';
            if ($sermon) {
                echo ' | <a class="button button-small" href="'
                    . get_edit_post_link($sermon->getID()) . '">'
                    . __('Edit')
                    . '</a>'
                    . ' <a class="button button-small" target="_blank" href="'
                    . get_permalink($sermon->getID()) . '">'.__('Sermon page', 'pulpit').'</a>'
                    . '<br />';
            } else {
                echo ' | <a class="button button-small" href="'
                    . admin_url('post-new.php?post_type=pulpit_sermon') . '">'
                    . __('New Sermon', 'pulpit') . '</a><br />';
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    public function render()
    {
        $this->renderList($this->eventRepository->getNext(5), __('Next events', 'pulpit'));
        $this->renderList($this->eventRepository->getLast(5), __('Last events', 'pulpit'));
        echo '<hr /><a class="button button-small" href="' . admin_url('post-new.php?post_type=pulpit_event') . '">'
            . __('New Event', 'pulpit') . '</a> <a class="button button-small" href="' . admin_url('admin.php?page=pulpit_import') . '">'
            .__('Import', 'pulpit').'</a>';
    }

}

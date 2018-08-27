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

namespace Peregrinus\Pulpit\Fields;

use Peregrinus\Pulpit\Domain\Model\EventModel;
use Peregrinus\Pulpit\Domain\Repository\EventRepository;

class EventsRelationField extends AbstractField
{

    /** @var array $locations Locations */
    protected $allEvents = [];

    public function __construct($key, $label = '', $context = '')
    {
        parent::__construct($key, $label, $context);
        add_action('admin_enqueue_scripts', [$this, 'enqueueResources']);
    }

    public function enqueueResources()
    {
        wp_enqueue_script('events-relation-field',
            PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/Fields/EventsRelationField.js');
    }

    /**
     * @param string $index
     * @param string $label Label string
     * @return string
     */
    public function renderLabel($index, $label)
    {
        return '<label for="' . $this->key . '[' . $index . ']">' . $this->label . '</label>';
    }

    public function getFieldName($index = 0)
    {
        return ($this->getContext() ? $this->getContext() . '[' . $this->getKey() . ']' : $this->getKey())
            .'[' . $index . ']';
    }

    protected function getFieldId($index)
    {
        return $this->getKey() . '_' . $index;
    }

    public function renderSingleForm($index, $eventId)
    {
        $o = '<div class="pulpit-events-relation-form-single">'
            . $this->renderLabel('event', $index, __('Event', 'pulpit'))
            . '<select id="' . $this->getFieldId($index) . '" name="'
            . $this->getFieldName($index) . '"  style="width: 100%"><option></option>';

        /** @var EventModel $event */
        foreach ($this->allEvents as $event) {
            if ($event) {
                $o .= '<option value="' . $event->getID() . '" '
                    . ($eventId == $event->getID() ? 'selected' : '')
                    .'>'
                    . $event->getFormattedDateTime(__('%Y-%m-%d %H:%M', 'pulpit')).', '
                    . ($event->getLocation() ? $event->getLocation()->getPostTitle() : '').': '
                    . $event->getPostTitle()
                    . '</option>';
            }
        }

        $o .= '</select>'
            .'<a class="button button-small pulpit-events-relation-field-btn-remove" href="#">' . __('Remove event',
                'pulpit') . '</a>'
            . '<hr /></div>';
        return $o;
    }

    /**
     * @return array Empty record
     */
    protected function getEmptyRecord()
    {
        return [];
    }


    /**
     * Output this field's form element
     *
     * @param array $value Custom field values
     *
     * @return string HTML output
     */
    public function render($values)
    {
        $events = maybe_unserialize($this->getValue($values, true)) ?: $this->getEmptyRecord();

        if (!count($this->allEvents)) {
            $this->allEvents = (new EventRepository())->get();
        }
        $o = '';

        $o .= '<div class="pulpit-event-relation-form" data-key="' . $this->getKey() . '">';

        $o .= '<script> if (eventRelationFormEmptyRecord == undefined) var eventRelationFormEmptyRecord = {}; '
            . 'eventRelationFormEmptyRecord[\'' . $this->getKey() . '\'] = \''
            . $this->renderSingleForm('###INDEX###', $this->getEmptyRecord())
            . '\'; </script>';

        foreach ($events as $index => $event) {
            if (is_numeric($event)) {
                $o .= $this->renderSingleForm($index, $event);
            }
        }

        $o .= '<br /><a class="button button-small pulpit-events-relation-field-btn-add" href="#">' . __('Add event',
                'pulpit') . '</a>';

        $o .= '</div>';

        return $o;
    }

    public function save($postId)
    {
        delete_post_meta($postId, $this->key);
        foreach ($this->getValueFromPOST() as $meta) {
            add_post_meta($postId, $this->key, $meta, false);
        }
    }

}

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

namespace Peregrinus\Pulpit\Admin\AjaxActions;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Repository\AgendaRepository;
use Peregrinus\Pulpit\Domain\Repository\EventRepository;
use Peregrinus\Pulpit\Fields\DetailedLiturgyField;

class ImportAgendaItemsAjaxAction extends AbstractAjaxAction
{
    public function do()
    {
        $field = new DetailedLiturgyField($_POST['key'], [
            'public_info' => __('This information may be published (e.g. in handouts)'),
            'responsible' => __('Responsible for this item'),
            'instructions_for' => __('Instructions for %s'),
        ]);

        list ($sourceType, $sourceId) = explode(':', $_POST['source']);
        switch ($sourceType) {
            case 'e':
                $items = $this->getItemsFromEvent($sourceId);
                break;
            case 'a':
                $items = $this->getItemsFromAgenda($sourceId);
                break;
        }

        $index = $_POST['index'];
        foreach ($items as $item) {
            echo $field->renderSingleForm($index, $item);
            $index++;
        }

        //echo '<li>' . print_r($items, 1) . '</li>';
        wp_die();
    }

    protected function getItemsFromEvent($source)
    {
        $event = (new EventRepository())->findByID($source);
        return $event->getLiturgy();
    }

    protected function getItemsFromAgenda($source)
    {
        $agenda = (new AgendaRepository())->findByID($source);
        $items = [];
        foreach ($agenda->getAgendaItems() as $item) {
            $item = maybe_unserialize($item);
            $item['optional'] = $item['optional'] ? 1 : 0;
            $item['data'] = '';
            $item['public_info'] = false;
            $item['responsible'] = '';
            foreach ($this->instructionsFor as $recipient) {
                $item['instructions'][$recipient] = '';
            }
            $items[] = $item;
        }
        return $items;
    }
}

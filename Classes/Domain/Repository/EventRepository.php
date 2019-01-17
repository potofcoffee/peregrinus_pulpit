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

namespace Peregrinus\Pulpit\Domain\Repository;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\EventModel;

class EventRepository extends AbstractRepository
{

    protected function getDatedQuery(string $dateString, string $compare, string $order)
    {
        return [
            'meta_key' => 'date',
            'orderby' => 'meta_value',
            'order' => $order,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'date',
                    'value' => date('Y-m-d', ($dateString ?: time())),
                    'compare' => $compare,
                    'type' => 'DATE'
                ]
            ]
        ];
    }

    public function getNext($number = 1)
    {
        $query = $this->getDatedQuery('', '>=', 'ASC');
        $query['posts_per_page'] = $number;
        return $this->get($query);
    }

    public function getLast($number = 1)
    {
        $query = $this->getDatedQuery('', '<', 'DESC');
        $query['posts_per_page'] = $number;
        return $this->get($query);
    }

}

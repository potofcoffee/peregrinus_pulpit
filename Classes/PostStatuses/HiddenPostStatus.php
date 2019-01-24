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

namespace Peregrinus\Pulpit\PostStatuses;

class HiddenPostStatus extends AbstractPostStatus
{
    public function __construct()
    {
        $this->label = __('Hidden', 'pulpit');
        $this->labelCount = _n_noop(
            'Hidden <span class="count">(%s)</span>',
            'Hidden <span class="count">(%s)</span>' ,
            'pulpit'
        );

        // for Potter:
        // __('Hidden <span class="count">(%s)</span>', 'pulpit');
        // __('Hidden <span class="count">(%s)</span>', 'pulpit');

        parent::__construct();
    }

}

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

class LocationRelationField extends AbstractField
{
    public function render($values)
    {
        $value = $this->getValue($values);
        if (!$value) $value == -1;

        $locations = get_posts(['post_type' => PEREGRINUS_PULPIT.'_location', 'posts_per_page' => -1]);

        $o = $this->renderLabel() . '<select id="' . $this->getKey() . '" name="' . $this->getFieldName(). '" style="width: 100%">';
        $o .= '<option value="-1"></option>';
        foreach ($locations as $location) {
            $o .= '<option value="'.$location->ID.'" '
                .($value == $location->ID  ? 'selected' : '')
                .'>'
                .$location->post_title.'</option>';
        }
        $o .= '</select>';
        //$o .= '<pre>'.print_r($value).'</pre>';

        //return 'Locations: <pre>'.print_r($locations, 1).'</pre>';
        return $o;

    }

}

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

class HandoutFormatField extends AbstractField
{
    public function render($values)
    {
        $value = $this->getValue($values);
        if (!$value) $value == -1;

        $formats = [];
        foreach (glob(PEREGRINUS_PULPIT_BASE_PATH.'Resources/Private/Templates/PostTypes/Sermon/Handout/*.html') as $file) {
            $formats[] = pathinfo($file, PATHINFO_FILENAME);
        }
        sort($formats);


        $o = $this->renderLabel() . '<select id="' . $this->getKey() . '" name="' . $this->getFieldName(). '" style="width: 100%">';
        $o .= '<option value="-1"></option>';
        foreach ($formats as $format) {
            $o .= '<option value="'.$format.'" '
                .($value == $format  ? 'selected' : '')
                .'>'
                .$format.'</option>';
        }
        $o .= '</select>';
        //$o .= '<pre>'.print_r($value).'</pre>';

        //return 'Locations: <pre>'.print_r($locations, 1).'</pre>';
        return $o;

    }

}

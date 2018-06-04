<?php
/*
 * PULPIT
 * A sermon plugin for WordPress
 *
 * Copyright (c) 2017 Christoph Fischer, http://www.peregrinus.de
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

class TextAreaField extends AbstractField
{

    protected $rows = 5;

    public function __construct($key, $label, $rows = 5, $context='')
    {
        parent::__construct($key, $label, $context);
        $this->setRows($rows);
    }

    public function renderLabel()
    {
        return $this->label ? '<label for="' . $this->key . '">' . $this->label . '</label><br />' : '';
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
        return $this->renderLabel() . '<textarea style="width: 100%" rows="' . $this->rows . '" id="'.$this->getKey().'" name="' . $this->getFieldName() . '">' . htmlentities($this->getValue($values)) . '</textarea><br />';
    }

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param int $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }

}

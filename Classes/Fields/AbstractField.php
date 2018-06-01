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


class AbstractField
{

    protected $key = '';
    protected $label = '';
    protected $context = '';

    public function __construct($key, $label = '', $context = '')
    {
        $this->setKey($key);
        $this->setLabel($label);
        $this->setContext($context);
    }

    /**
     * Register
     * This function is called upon registration and may serve to add more steps
     */
    public function register()
    {

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
        return '';
    }

    /**
     * Render the label for this form element
     * @return string Rendered label
     */
    public function renderLabel()
    {
        return $this->label ? '<label for="' . $this->key . '">' . $this->label . '</label>' : '';
    }

    /**
     * Get field name
     * @return string Field name
     */
    public function getFieldName()
    {
        return $this->getContext() ? $this->getContext() . '[' . $this->getKey() . ']' : $this->getKey();
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get value from Array
     * @param $values Value
     */
    public function getValue($values)
    {
        $value = $values[$this->getKey()];
        if (is_array($value)) {
            $value = $value[0];
        }
        return $value;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Save this field's metadata in a post
     * @param string $postId ID of the post
     */
    public function save($postId)
    {
        update_post_meta($postId, $this->key, $this->getValueFromPOST());
    }

    /**
     * Get this field's metadata from POST
     * @return mixed Metadata value
     */
    public function getValueFromPOST()
    {
        return $_POST[$this->key];
    }


}
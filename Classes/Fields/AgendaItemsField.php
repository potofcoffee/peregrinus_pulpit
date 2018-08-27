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

use Peregrinus\Pulpit\AgendaItems\AgendaItemFactory;

class AgendaItemsField extends AbstractField
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
        wp_enqueue_style( 'jquery-ui' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script('agenda-item-field',
            PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/Fields/AgendaItemsField.js');
    }

    /**
     * @param string $index
     * @param string $label Label string
     * @return string
     */
    public function renderLabel($index, $subKey)
    {
        return '<label for="' . $this->key . '[' . $index . '][' . $subKey . ']">' . $this->label[$subKey] . '</label>';
    }

    public function getFieldName($index = 0, $subKey)
    {
        return ($this->getContext() ? $this->getContext() . '[' . $this->getKey() . ']' : $this->getKey())
            . '[' . $index . '][' . $subKey . ']';
    }

    protected function getFieldId($index, $subKey)
    {
        return $this->getKey() . '_' . $index . '_' . $subKey;
    }

    public function renderSingleForm($index, $item)
    {
        // TODO: Fields: title, type, description, optional

        $o = '<li class="pulpit-agenda-items-form-single"><span class="pulpit-agenda-items-form-single-toggle ui-icon ui-icon-arrowthick-1-n"></span>'
            . $this->renderLabel($index, 'title')
            . '<input style="width:100%" type="text" id="' . $this->getFieldId($index,
                'title') . '" name="' . $this->getFieldName($index, 'title') . '" value="' . $item['title'] . '" />'
            .'<div class="pulpit-agenda-items-form-sub">'
            . $this->renderLabel($index, 'type')
            . AgendaItemFactory::selectBox($this->getFieldId($index, 'type'), $this->getFieldName($index, 'type'),
                $item['type'])
            . $this->renderLabel($index, 'description')
            . '<textarea style="width:100%" id="' . $this->getFieldId($index,
                'description') . '" name="' . $this->getFieldName($index,
                'description') . '">' . $item['description'] . '</textarea>'
            . '<input type="checkbox" id="' . $this->getFieldId($index,
                'optional') . '" name="' . $this->getFieldName($index,
                'optional') . '" ' . ($item['optional'] ? 'checked' : '') . ' />'
            . $this->renderLabel($index, 'optional') . '<br /><br />'
            . '<a class="button button-small pulpit-agenda-items-field-btn-remove" href="#">' . __('Remove agenda item',
                'pulpit') . '</a></div>'
            . '</li>';
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
        $items = $this->getValue($values, true) ?: $this->getEmptyRecord();

        $o = '';

        $o .= '<ul class="pulpit-agenda-items-form" data-key="' . $this->getKey() . '">';

        $o .= '<script> if (agendaItemsFormEmptyRecord == undefined) var agendaItemsFormEmptyRecord = {}; '
            . 'agendaItemsFormEmptyRecord[\'' . $this->getKey() . '\'] = \''
            . $this->renderSingleForm('###INDEX###', $this->getEmptyRecord())
            . '\'; </script>';

        foreach ($items as $index => $item) {
            $item = maybe_unserialize($item);
            $o .= $this->renderSingleForm($index, $item);
        }

        $o .= '<br /><a class="button button-small pulpit-agenda-items-field-btn-add" href="#">' . __('Add item',
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

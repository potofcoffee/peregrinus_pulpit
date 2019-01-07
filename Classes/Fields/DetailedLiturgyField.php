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

use Peregrinus\Pulpit\AgendaItems\AbstractAgendaItem;
use Peregrinus\Pulpit\AgendaItems\AgendaItemFactory;
use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\AgendaModel;
use Peregrinus\Pulpit\Domain\Repository\AgendaRepository;
use Peregrinus\Pulpit\Domain\Repository\EventRepository;

class DetailedLiturgyField extends AbstractField
{

    /** @var array $locations Locations */
    protected $instructionsFor = [];

    public function __construct($key, $label = [], $context = '')
    {
        parent::__construct($key, $label, $context);
        add_action('admin_enqueue_scripts', [$this, 'enqueueResources']);
    }

    public function enqueueResources()
    {
        wp_enqueue_style('jquery-ui');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
            array('jquery'));
        wp_enqueue_script('detailed-liturgy-field',
            PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Scripts/Admin/Fields/DetailedLiturgyField.js');
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

    protected function hiddenField($index, $key, $value)
    {
        return '<input type="hidden" name="' . $this->getFieldName($index, $key) . '" value="' . $value . '" />';
    }

    public function renderSingleForm($index, $item)
    {
        // TODO: Fields: title, type, description, optional

        /** @var AbstractAgendaItem $agendaItem */
        $agendaItem = $item['type'] ? AgendaItemFactory::get($item['type']) : new AbstractAgendaItem();

        $o = '<li class="pulpit-detailed-liturgy-form-single">'
            . '<span class="pulpit-detailed-liturgy-field-btn-remove pulpit-collapse-section-icon ui-icon ui-icon-trash"></span> '
            . $agendaItem->renderTitle($item['title'] ?: '###TITLE###')
            . '<span class="pulpit-detailed-liturgy-form-data-preview" id="' . $this->getFieldId($index,
                'data-preview') . '">'
            . $agendaItem->renderDataPreview($item['data'])
            . '</span>'
            . '<div class="pulpit-detailed-liturgy-form-sub" style="display:none;" data-preview="' . $this->getFieldId($index,
                'data-preview') . '">'
            . $this->hiddenField($index, 'title', $item['title'])
            . $this->hiddenField($index, 'type', $item['type'])
            . $this->hiddenField($index, 'description', $item['description'])
            . $this->hiddenField($index, 'optional', $item['optional']);
        if ($agendaItem->hasFields()) {
            $o .= $agendaItem->renderDataForm(
                    $this->getFieldId($index, 'data'),
                    $this->getFieldName($index, 'data'),
                    $item['data']
                )
                . '<input type="checkbox" id="' . $this->getFieldId($index,
                    'public_info') . '" name="' . $this->getFieldName($index,
                    'public_info') . '" ' . ($item['public_info'] ? 'checked' : '') . ' />'
                . $this->renderLabel($index, 'public_info') . '<br />'
                . $this->renderLabel($index, 'responsible')
                . '<input style="width:100%" type="text" id="' . $this->getFieldId($index,
                    'responsible') . '" name="' . $this->getFieldName($index,
                    'responsible') . '" value="' . $item['responsible'] . '" />';

            foreach ($this->instructionsFor as $recipient) {
                $recipientKey = 'instructions][' . $recipient;
                $o .= '<label for="' . $this->key . '[' . $index . '][instructions][' . $recipient . ']">'
                    . sprintf($this->label['instructions_for'], $recipient)
                    . '</label>'
                    . '<textarea style="width:100%" id="' . $this->getFieldId($index,
                        $recipientKey) . '" name="' . $this->getFieldName($index,
                        $recipientKey) . '">' . $item[$recipientKey] . '</textarea>';
            }
        }

        $o .= '</div>'
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

    protected function renderAgendaSelect(): string
    {
        $agendas = (new AgendaRepository())->get();
        $o = '<select style="height:24px; line-height: 22px;" id="' . $this->getFieldId('', 'import') . '">';
        $o .= '<optgroup label="'.__('Agendas', 'pulpit').'">';
        /** @var AgendaModel $agenda */
        foreach ($agendas as $agenda) {
            $o .= '<option value="a:' . $agenda->getID() . '">' . $agenda->getTitle() . '</option>';
        }
        $o .= '</optgroup>';

        $o .= '<optgroup label="'.__('Events', 'pulpit').'">';
        /** @var AgendaModel $agenda */
        foreach ((new EventRepository())->get() as $event) {
            $o .= '<option value="e:' . $event->getID() . '">' . $event->getDate().' '.$event->getTime().' '.$event->getTitle() . '</option>';
        }
        $o .= '</optgroup>';

        $o .= '</select>';
        return $o;
    }

    protected function import($id): array
    {
        return [];
    }

    protected function toolBar()
    {
        $o = '<div class="pulpit-detailed-liturgy-field-toolbar">';
        /** @var AbstractAgendaItem $item */
        foreach (AgendaItemFactory::getAll() as $item) {
            $o .= $item->renderToolBarButton($this->key);
        }
        $o .= '<a class="button button-small pulpit-detailed-liturgy-field-btn-remove-all" href="#" title="'
            . __('Remove all', 'pulpit')
            .'"><span class="fa fa-trash"></span></a>';
        $o .= $this->renderAgendaSelect();
        $o .= '<a class="button button-small pulpit-detailed-liturgy-field-btn-import" href="#" data-source="#'
            .$this->getFieldId('', 'import').'"  data-key="'.$this->key.'" title="'.__('Import', 'pulpit')
            .'">'
            .'<span class="fa fa-file-import"></span>'
            . '</a>';
        $o .= '<hr /></div>';
        return $o;
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
        $this->instructionsFor = explode(',', get_option(PEREGRINUS_PULPIT . '_general')['agenda_instructions']);

        $items = $this->getValue($values, true) ?: $this->getEmptyRecord();

        if (isset($items[0])) {
            $items[0] = maybe_unserialize($items[0]);
            if (isset($items[0]['import'])) unset($items[0]);
        }

        $o = '';

        $o .= '<script> if (detailedLiturgyFormEmptyRecord == undefined) var detailedLiturgyFormEmptyRecord = {}; '
            . 'detailedLiturgyFormEmptyRecord[\'' . $this->getKey() . '\'] = \''
            . $this->renderSingleForm('###INDEX###', $this->getEmptyRecord())
            . '\'; </script>';

        $o .= $this->toolBar();

        $o .= '<ul class="pulpit-detailed-liturgy-form" data-key="' . $this->getKey() . '">';

        foreach ($items as $index => $item) {
            $item = maybe_unserialize($item);
            $o .= $this->renderSingleForm($index, $item);
        }

        $o .= '</ul></div>';

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

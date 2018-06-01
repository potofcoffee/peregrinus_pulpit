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


use Peregrinus\Pulpit\Admin\FieldPreviewRenderer;

class FileRelationField extends AbstractField
{
    /** @var string $buttonTitle Title of the submit button for the upload window */
    protected $buttonTitle = '';

    /** @var string $dialogTitle Title of the media dialog */
    protected $dialogTitle = '';

    /** @var string $mimeType Mime type */
    protected $mimeType = '';

    public function __construct(
        $key,
        string $label = '',
        string $mimeType = '',
        string $buttonTitle = '',
        string $dialogTitle = '',
        string $context = ''
    ) {
        parent::__construct($key, $label, $context);
        $this->mimeType = $mimeType;
        $this->buttonTitle = $buttonTitle;
        $this->dialogTitle = $dialogTitle;
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
        $value = $this->getValue($values);
        if ($value) {
            $preview = FieldPreviewRenderer::render($value);
        } else {
            $preview = '';
        }

        $o = $this->renderLabel();
        $o .= '<div class="pulpit-upload-wrapper" data-attachment-id="' . $this->getValue($values) . '" data-button-title="' . $this->buttonTitle . '" data-field="' . $this->getFieldName() . '" data-dialog-title="' . $this->dialogTitle . '" data-mime-type="' . $this->mimeType . '">
        <input type="hidden" name="' . $this->getFieldName() . '" class="image_path" value="" id="pulpit-upload-data-' . $this->getFieldName() . '">
        <div class="pulpit-upload-preview ' . ($preview ? '' : 'pulpit-hide-on-load') . '">
        <span id="pulpit-upload-preview-' . $this->getFieldName() . '"><b>' . $preview . '</b></span>
<button class="button button-small pulpit-upload-clear-button"  data-field="' . $this->getFieldName() . '" title="Zuweisung löschen"><span class="dashicons dashicons-trash"></span></button>
</div>
<input type="button" value="' . __('Aufnahme auswählen',
                'pulpit') . '" class="button button-small pulpit-upload-button ' . ($preview ? 'pulpit-hide-on-load' : '') . '"/><br /></div>';
        return $o;
    }

    public function renderLabel()
    {
        $label = parent::renderLabel();
        return (trim($label) ? $label . '<br />' : '');
    }


}
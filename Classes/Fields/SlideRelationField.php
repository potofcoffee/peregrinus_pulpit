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

use Peregrinus\Pulpit\Admin\FieldPreviews\SlidesFieldPreview;

class SlideRelationField extends AbstractField
{
    /** @var string $buttonTitle Title of the submit button for the upload window */
    protected $buttonTitle = '';

    /** @var string $dialogTitle Title of the media dialog */
    protected $dialogTitle = '';

    /** @var string $mimeType Mime type */
    protected $mimeType = '';

    public function __construct(
        $key,
        string $label = null,
        string $mimeType = null,
        string $buttonTitle = null,
        string $dialogTitle = null,
        string $context = null
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
        if (trim($value)) {
            $slides = explode(',', $value);
        } else {
            $slides = [];
        }

        $o = $this->renderLabel();
        $o .= '<input type="text" id="' . $this->getKey() . '" name="' . $this->getFieldName() . '" value="' . htmlentities($this->getValue($values)) . '" style="width: 100%"><br />';
        $o .= '<div class="pulpit-slides-preview">';
        foreach ($slides as $slideId) {
            $o .= SlidesFieldPreview::render(get_post($slideId), get_post_meta($slideId));
        }
        $o .= '</div>';
        $o .= '<input type="button" value="' . __('Add new slide',
                'pulpit') . '" class="button button-small open-slider-custom-modal"/><br /></div>';
        return $o;
    }

    public function renderLabel()
    {
        $label = parent::renderLabel();
        return (trim($label) ? $label . '<br />' : '');
    }

}

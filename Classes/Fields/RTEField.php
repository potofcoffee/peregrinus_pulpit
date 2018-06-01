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


class RTEField extends TextAreaField
{


    public function register()
    {
        add_action('admin_print_footer_scripts', [$this, 'script'], 99);
    }

    public function script()
    {
        echo "
			<script type=\"text/javascript\">/* <![CDATA[ */
				jQuery(function($){
					var i=1;
					$('textarea." . PEREGRINUS_PULPIT . "_rtefield').each(function(e)
					{
					  var id = $(this).attr('id');
					  if (!id)
					  {
					   id = 'customEditor-' + i++;
					   $(this).attr('id',id);
					  }
					  tinyMCE.execCommand('mceAddControl', false, id);
					  tinyMCE.execCommand('mceAddControl', false, id);
					});
				});
			/* ]]> */</script>		
";
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
        return $this->renderLabel() . '<br /><textarea class="' . PEREGRINUS_PULPIT . '_rtefield" id="field_' . $this->key . '" style="width: 100%" rows="' . $this->rows . '" name="' . $this->key . '">' . htmlentities($values[$this->key][0]) . '</textarea><br />';
    }


}
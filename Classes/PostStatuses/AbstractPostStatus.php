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

use Peregrinus\Pulpit\AbstractObject;
use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Utility\StringUtility;
use Symfony\Component\Debug\Debug;

class AbstractPostStatus extends AbstractObject
{

    /** @var string $label */
    protected $label = '';

    /** @var bool $public */
    protected $public = false;

    /** @var bool $private */
    protected $private = false;

    /** @var bool $internal */
    protected $internal = false;

    /** @var bool $excludeFromSearch */
    protected $excludeFromSearch = false;

    /** @var bool $showInAdminAllList */
    protected $showInAdminAllList = true;

    /** @var bool $showInAdminStatusList */
    protected $showInAdminStatusList = true;

    /** @var array $config */
    protected $config = [];

    public function __construct()
    {
        foreach (get_object_vars($this) as $key => $var) {
            if ($key != 'config') {
                $this->config[StringUtility::CamelCaseToUnderscore($key)] = $this->$key;
            }
        }

        add_action('admin_footer-post.php', [$this, 'addNecessaryJS']);
    }

    public function register() {
        register_post_status(PEREGRINUS_PULPIT.'_'.$this->getKey(), $this->config);
    }

    public function addNecessaryJS() {
        global $post;
        $complete = '';
        $label = '';
        if($post->post_status == PEREGRINUS_PULPIT.'_'.$this->getKey()){
            $complete = ' selected="selected"';
            $label = '<span id="post-status-display"> '.$this->label.'</span>';
        }
        echo '
<script>
jQuery(document).ready(function($){
$("select#post_status").append("<option value=\"'.PEREGRINUS_PULPIT.'_'.$this->getKey().'\" '.$complete.'>'.$this->label.'</option>");
$(".misc-pub-section label").append("'.$label.'");
});
</script>
';
    }

}

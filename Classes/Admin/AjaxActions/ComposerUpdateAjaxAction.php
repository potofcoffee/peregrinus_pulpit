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


namespace Peregrinus\Pulpit\Admin\AjaxActions;


use Peregrinus\Pulpit\Installer\ComposerWrapper;

class ComposerUpdateAjaxAction extends AbstractAjaxAction
{
    public function do()
    {
        $updateUrl = get_admin_url(null, 'options-general.php?page=pulpit-settings-general&composerUpdate=1');

        $composer = new ComposerWrapper();
        $composer->do('update --no-interaction');
        if ($composer->isOutdated()) {
            $res = [
                'success' => false,
                'notice' => __('<strong>Oops!</strong> It looks like something went wrong during the update process. You can <a href="'.$updateUrl.'">click here</a> to try again.')
            ];
        } else {
            $res = [
                'success' => true,
                'notice' => __('Good news! All necessary updates have been installed.')
            ];
        }
        echo json_encode($res);
        wp_die();
    }


}
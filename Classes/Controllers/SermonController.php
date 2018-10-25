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

namespace Peregrinus\Pulpit\Controllers;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\SermonModel;
use Peregrinus\Pulpit\Domain\Repository\SermonRepository;

class SermonController extends AbstractController
{

    /** @var SermonRepository */
    protected $sermonRepository = null;

    public function __construct()
    {
        parent::__construct();
        $this->sermonRepository = new SermonRepository();
    }

    protected function transformQueryObject($queryObject): SermonModel
    {
        return new SermonModel($queryObject);
    }

    public function singleAction(SermonModel $sermon)
    {

        $this->view->assign('isPreview', (($sermon->getPost()->post_status == 'future')  || $_GET['forcePreview']) &&  !$_GET['forcePublished']);
        $this->view->assign('isPublished', (($sermon->getPost()->post_status == 'publish') || $_GET['forcePublished']) && !$_GET['forcePreview']);
        $this->view->assign('sermon', $sermon);
    }

    public function archiveAction()
    {
        $sermons = $this->sermonRepository->get();
        $this->view->assign('sermons', $sermons);
    }

    public function handoutAction(SermonModel $sermon)
    {
        $layout = filter_var($_GET['template'], FILTER_SANITIZE_STRING) ?: $sermon->getHandoutFormat();
        if ($layout == -1) $layout = get_option('default_handout_layout');
        if (isset($_GET['underlineMode'])) {
            $this->view->assign('underlineMode', filter_var($_GET['underlineMode'], FILTER_SANITIZE_STRING));
        }
        if (isset($_GET['events'])) {
            $events = $_GET['events'];
            foreach ($events as $key => $val) $events[$key] = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
            $sermon->setEventsMeta($events);
        }
        $this->view->assign('sermon', $sermon);
        $this->setAction('Handout/' . $layout);
    }

    public function configureHandoutAction(SermonModel $sermon) {

        foreach (glob(PEREGRINUS_PULPIT_BASE_PATH.'Resources/Private/Templates/PostTypes/Sermon/Handout/*.html') as $file) {
            $formats[] = pathinfo($file, PATHINFO_FILENAME);
        }
        $underlineMode = filter_var($_GET['underlineMode'], FILTER_SANITIZE_STRING) ?: 'blank';
        $this->view->assign('sermon', $sermon);
        $this->view->assign('formats', $formats);
        $this->view->assign('underlineMode', $underlineMode);
    }

}

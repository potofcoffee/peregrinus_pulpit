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


namespace Peregrinus\Pulpit\CustomFormats;


use Peregrinus\Pulpit\View;

class AbstractPackagedCustomFormat extends AbstractTemplatedCustomFormat
{

    protected $tempFolder = '';
    protected $tempKey = '';
    /** @var \ZipArchive  */
    protected $zip = null;

    public function __construct()
    {
        $this->tempKey = md5(time());
        $this->tempFolder = PEREGRINUS_PULPIT_BASE_PATH . 'Temp/' . $this->tempKey . '/';
    }

    protected function writeToFile(string $fileName, $contents, $addToZip = true)
    {
        file_put_contents($this->tempFolder . $fileName, $contents);
        if ($addToZip) $this->addFile($fileName);
    }

    protected function renderViewToFile(View $view, string $viewName, string $fileName)
    {
        $this->writeToFile($fileName, $view->render($viewName));
    }

    protected function createContainer(string $fileName)
    {
        if (!is_dir($this->tempFolder)) mkdir($this->tempFolder, 0777, true);

        // Initialize archive object
        $this->zip = new \ZipArchive();
        $this->zip->open(PEREGRINUS_PULPIT_BASE_PATH.'Temp/'.$fileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

    }

    /**
     * Add a file to the container
     * @param string $file File path
     * @param bool $absolutePath Optional: true if above path is absolute (by default it is relative to current temp folder
     * @param string $alternativeName Optional: new file name
     */
    protected function addFile(string $file, bool $absolutePath = false, $alternativeName = '')  {
        $this->zip->addFile(($absolutePath ? '' : $this->tempFolder).$file, ($alternativeName ?: $file));
    }

    protected function closeContainer() {
        $this->zip->close();
    }

    protected function deleteTempFolder($dir = '') {
        if ($dir == '') $dir = $this->tempFolder;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir.'/'.$object) == 'dir') $this->deleteTempFolder($dir.'/'.$object); else unlink($dir.'/'.$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }    
    
}
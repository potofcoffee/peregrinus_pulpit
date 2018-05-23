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


namespace Peregrinus\Pulpit\ViewHelpers\Pulpit\Output;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class PdfViewHelper extends AbstractViewHelper
{

    /**
     * @var boolean
     */
    protected $escapeChildren = false;
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('filename', 'string', 'File name');
        $this->registerArgument('layout', 'string', 'Layout');
        $this->registerArgument('orientation', 'string', 'Orientation');
        $this->registerArgument('leftmargin', 'string', 'Left margin');
        $this->registerArgument('topmargin', 'string', 'Top margin');
        $this->registerArgument('bottommargin', 'string', 'bottom margin');
        $this->registerArgument('rightmargin', 'string', 'Right margin');
        $this->registerArgument('debug', 'bool', 'Debug mode');
    }

    protected function render()
    {
        $slug = str_replace(' ', '_', pathinfo($this->arguments['filename'], PATHINFO_FILENAME) . '.' . md5(time()));
        $tempFileName = $slug . '.html';
        $commandFile    = PEREGRINUS_PULPIT_BASE_PATH . 'bin/wkhtmltopdf-amd64';
        //$commandFile = '/usr/bin/wkhtmltopdf';
        $debugFile = PEREGRINUS_PULPIT_BASE_PATH . 'Temp/' . $slug . '.txt';
        $tempFilePath = PEREGRINUS_PULPIT_BASE_PATH . 'Temp/' . $tempFileName;
        $outputFilePath = PEREGRINUS_PULPIT_BASE_PATH . 'Temp/' . pathinfo($tempFileName,
                PATHINFO_FILENAME) . '.pdf';

        file_put_contents($tempFilePath, $this->renderChildren());

        $options = ['encoding' => 'utf-8'];
        if (isset($this->arguments['orientation'])) {
            $options['orientation'] = $this->arguments['orientation'];
        }
        if (isset($this->arguments['topmargin'])) {
            $options['margin-top'] = $this->arguments['topmargin'];
        }
        if (isset($this->arguments['leftmargin'])) {
            $options['margin-left'] = $this->arguments['leftmargin'];
        }
        if (isset($this->arguments['rightmargin'])) {
            $options['margin-right'] = $this->arguments['rightmargin'];
        }
        if (isset($this->arguments['bottommargin'])) {
            $options['margin-bottom'] = $this->arguments['bottommargin'];
        }

        if (isset($this->arguments['layout'])) {
            $options['page-size'] = $this->arguments['layout'];
        }


        // create command
        $command = $commandFile;
        foreach ($options as $key => $value) {
            $command .= ' --' . $key . ' ' . $value;
        }

        $command .= ' ' . $tempFilePath . ' ' . $outputFilePath;

        exec($command . ' 2>&1', $debugOutput);
        file_put_contents($debugFile, $command . PHP_EOL . PHP_EOL . PHP_EOL . print_r($debugOutput, 1));
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $this->arguments['filename'] . '"');
        //header( 'Content-Disposition: attachment; filename="' . basename( $outputFilePath ) . '"' );
        echo file_get_contents($outputFilePath);

        if (!$this->arguments['debug']) {
            unlink($outputFilePath);
            unlink($tempFilePath);
            unlink($debugFile);
        }

        exit();
    }


}

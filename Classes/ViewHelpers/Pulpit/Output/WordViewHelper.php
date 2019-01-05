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

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\Style\Language;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use PhpOffice\PhpWord\PhpWord;

/**
 * Class WordViewHelper
 * Provides a ViewHelper that renders content as Microsoft Word document, using PhpWord
 * @package Peregrinus\Pulpit\ViewHelpers\Pulpit\Output
 */
class WordViewHelper extends AbstractViewHelper
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
     * Register all available arguments
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('filename', 'string', 'File name');
        $this->registerArgument('debug', 'bool', 'Debug mode');
        $this->registerArgument('defaultFont', 'string', '');
        $this->registerArgument('defaultFontSize', 'string', '');
        $this->registerArgument('language', 'string', '');
    }

    /**
     * Render this ViewHelper
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    protected function render()
    {
        // if ($this->arguments['debug']) return $this->renderChildren();

        $phpWord = new PhpWord();

        // set document default language
        if ($this->arguments['language']) {
            $phpWord->getSettings()->setThemeFontLang(new Language($this->arguments['language']));
        }

        if ($this->arguments['defaultFont']) $phpWord->setDefaultFontName($this->arguments['defaultFont']);
        if ($this->arguments['defaultFontSize']) $phpWord->setDefaultFontSize($this->arguments['defaultFontSize']);


        $section = $phpWord->addSection();
        $this->renderingContext->getVariableProvider()->add('__PhpWord', $phpWord);
        $this->renderingContext->getVariableProvider()->add('__PhpWord_Section', $section);


        $this->renderChildren();

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename="'.$this->arguments['filename'].'"');
        try {
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save('php://output');
        } catch (Exception $exception) {
            return '<b>Error creating the Word document:</b> '.$exception->getMessage();
        }
    }

}

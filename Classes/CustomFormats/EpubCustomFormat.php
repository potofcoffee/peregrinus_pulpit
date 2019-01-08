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

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Domain\Model\SermonModel;
use Peregrinus\Pulpit\Utility\UUIDUtility;
use Peregrinus\Pulpit\View;
use Peregrinus\Pulpit\Fluid\DynamicVariableProvider;

class EpubCustomFormat extends AbstractPackagedCustomFormat
{
    protected $viewName = 'epub';

    /**
     * Split the sermon text into chapters
     * @param SermonModel $sermon Sermon
     * @return array Chapters and titles
     */
    protected function splitChapters(SermonModel $sermon) {
        $text = $sermon->getContent()['extended'];

        preg_match_all('/<h3>(.*)<\/h3>/m', $text, $tmp);

        // do we have a beginning without heading?
        if ((!count($tmp[0])) || (strpos($text, $tmp[0][0]) > 0)) {
            array_unshift($tmp[1], $sermon->getTitle());
            array_unshift($tmp[0], '<h3>'.$sermon->getTitle().'</h3>');
            $text = '<h3>'.$sermon->getTitle().'</h3>'.$text;
        }

        foreach ($tmp[1] as $index => $title) {
            // cut off heading
            $text = substr($text, strlen($tmp[0][$index]));

            // get the text
            if ($index == count($tmp[1])-1) {
                $chapter = $text;
            } else {
                $nextMatch = $tmp[0][$index+1];
                $chapter = substr($text, 0, strpos($text, $tmp[0][$index+1]));
            }

            $chapters[] = [
                'title' => $title,
                'chapter' => $chapter,
                'ID' => $index,
            ];

            // cut off chapter
            $text = substr($text, strlen($chapter));
        }
        $chapters[0]['isFirst'] = true;
        $chapters[count($chapters) - 1]['isLast'] = true;

        return $chapters;
    }


    public function render()
    {
        $post = $this->getPost();
        $sermon = new SermonModel($post);

        $smashwords = filter_var($_GET['smashwords'], FILTER_SANITIZE_NUMBER_INT) ?: 0;


        $view = new View();
        $context = $view->getRenderingContext();
        $context->setVariableProvider(new DynamicVariableProvider());
        $this->prepareView($post, $view);

//        $slides = $this->getSlides($post);
        $slides = $this->splitChapters($sermon);
        $uuid = UUIDUtility::v4();

        $view->assign('slides', $slides);
        $view->assign('sermon', $sermon);
        $view->assign('uuid', $uuid);
        $view->assign('smashwords', $smashwords);

        $this->createContainer($this->tempKey . '.epub');

        // mimetype
        $this->writeToFile('mimetype', 'application/epub+zip');

        // write manifest
        mkdir($this->tempFolder . 'META-INF/');
        $this->renderViewToFile($view, 'Epub/Manifest', 'META-INF/container.xml');

        // cover
        $this->addFile(get_attached_file(get_post_thumbnail_id($post)), true, 'cover.jpg');

        // stylesheet
        $this->renderViewToFile($view, 'Epub/Styles', 'stylesheet.css');

        // opf
        $this->renderViewToFile($view, 'Epub/ContentOpf', 'content.opf');

        // title page
        $this->renderViewToFile($view, 'Epub/TitlePage', 'titlepage.xhtml');

        // front matter
        $this->renderViewToFile($view, 'Epub/FrontMatter', 'frontmatter.xhtml');

        // toc
        $this->renderViewToFile($view, 'Epub/toc', 'toc.xhtml');

        // nav
        $this->renderViewToFile($view, 'Epub/Nav', 'nav.xhtml');

        // chapters
        foreach ($slides as $index => $slide) {
            $view->assign('slide', $slide);
            $this->renderViewToFile($view, 'Epub/Chapter', 'slide_' . $slide['ID'] . '.xhtml');
        }

        // back matter
        $this->renderViewToFile($view, 'Epub/AuthorAbout', 'author_about.xhtml');
        $this->renderViewToFile($view, 'Epub/AuthorContact', 'author_contact.xhtml');

        // toc.ncx
        $this->renderViewToFile($view, 'Epub/TocNcx', 'toc.ncx');

        $this->closeContainer();
        $this->deleteTempFolder();

        $tempFile = PEREGRINUS_PULPIT_BASE_PATH . 'Temp/' . $this->tempKey . '.epub';
        $targetFile = strftime('%Y%m%d', strtotime($post->post_date)) . ' ' . $post->post_title . '.epub';

        header('Content-Type: application/epub+zip');
        header('Content-Disposition: attachment; filename="' . $targetFile . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($tempFile));
        ob_clean();
        flush();
        readfile($tempFile);
        unlink($tempFile);

        //Header( 'Location: '.PEREGRINUS_PULPIT_BASE_URL.'Temp/'.$this->tempKey.'.epub');
        //Header( 'Location: '.PEREGRINUS_PULPIT_BASE_URL.'Temp/'.$this->tempKey);
    }

    protected function prepareView(\WP_Post $post, View $view)
    {
        parent::prepareView($post, $view);
    }

    protected function getSlides(\WP_Post $post)
    {
        $slides = (array)get_posts([
            'post_type' => 'pulpit_slide',
            'post_parent' => $post->ID,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'numberposts' => -1,
        ]);
        foreach ($slides as $key => $slide) {
            $slides[$key] = (array)$slide;
            $slides[$key]['isFirst'] = false;
            $slides[$key]['isLast'] = false;
        }
        $slides[0]['isFirst'] = true;
        $slides[count($slides) - 1]['isLast'] = true;
        return $slides;
    }
}

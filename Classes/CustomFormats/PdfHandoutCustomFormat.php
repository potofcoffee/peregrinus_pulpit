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


namespace Peregrinus\Pulpit\CustomFormats;


use Peregrinus\Pulpit\View;

class PdfHandoutCustomFormat extends AbstractCustomFormat {

	/**
	 * Render the CustomFormat
	 */
	function render() {
		global $post;
		$post = get_post();
		$meta = get_post_meta($post->ID);
		$thumbnail = \has_post_thumbnail($post->ID) ? \get_the_post_thumbnail($post->ID) : null;

		$view = new View();
		$context = $view->getRenderingContext();
		$context->setControllerName('CustomView');

		$view->assign('post', $post);
		$view->assign('meta', $meta);
		$view->assign('underlineMode', $_GET['underlineMode'] ? $_GET['underlineMode'] : 'html5');
		$view->assign('thumbnail', $thumbnail);

		echo $view->render('pdfHandout');
		die();
	}

}
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


namespace Peregrinus\Pulpit\PostTypes;


use Peregrinus\Pulpit\Admin\MetaBox;
use Peregrinus\Pulpit\Fields\CheckBoxField;
use Peregrinus\Pulpit\Fields\InputField;
use Peregrinus\Pulpit\Fields\RTEField;
use Peregrinus\Pulpit\Fields\TextAreaField;

/**
 * Class SermonPostType
 * Custom PostType for a sermon
 * @package Peregrinus\Pulpit\PostTypes
 */
class SermonPostType extends AbstractPostType {

	public function __construct() {

		$this->labels = [
			'name'               => __( 'Sermons', 'pulpit' ),
			'singular_name'      => __( 'Sermon', 'pulpit' ),
			'add_new'            => __( 'Add New', 'pulpit' ),
			'add_new_item'       => __( 'Add New Sermon', 'pulpit' ),
			'edit_item'          => __( 'Edit Sermon', 'pulpit' ),
			'new_item'           => __( 'New Sermon', 'pulpit' ),
			'view_item'          => __( 'View Sermon', 'pulpit' ),
			'search_items'       => __( 'Search Sermons', 'pulpit' ),
			'not_found'          => __( 'No sermons found', 'pulpit' ),
			'not_found_in_trash' => __( 'No sermons found in Trash', 'pulpit' ),
			'menu_name'          => __( 'Sermons', 'pulpit' ),
		];

		$this->configuration = [
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'supports'           => [ 'title', 'editor', 'comments', 'thumbnail', 'entry-views' ],
		];

		parent::__construct();

	}

	/**
	 * Register custom columns
	 */
	public function registerCustomColumns() {
		add_action("manage_posts_custom_column",  [$this, 'getCustomColumns']);
		add_filter("manage_edit-portfolio_columns", [$this, 'renderCustomColumn']);
	}


	/**
	 * Custom columns for admin view
	 * @return array Columns
	 */
	public function getCustomColumns() {
		return [
			'cb' => '<input type="checkbox">',
			'date' => __('Preached', 'pulpit'),
			'title' => __('Title', 'pulpit'),
			'series' => __('Series', 'pulpit'),
			'preacher' => __('Preacher', 'pulpit'),
		];
	}

	/**
	 * Render a custom column
	 * @param string $column Column name
	 */
	public function renderCustomColumn($column) {
		global $post;
		$custom = get_post_custom();
		switch ($column) {
			case 'title':
				\the_title();
				echo '<br />'.$custom['subtitle'];
				break;
			case 'date':
				\the_date('Y-m-d');
				break;
			case 'series':
				echo \get_the_term_list($post->ID, PEREGRINUS_PULPIT.'_sermon_series', '', ', ', '');
				break;
			case 'preacher':
				echo \get_the_term_list($post->ID, PEREGRINUS_PULPIT.'_sermon_preacher', '', ', ', '');
				break;
		}
	}

	/**
	 * Add the custom fields for this post type
	 */
	public function addCustomFields() {
		return [
			new MetaBox( 'general', __('General', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
					new InputField( 'subtitle', __('Subtitle', 'pulpit') ),
				]
			),
			new MetaBox( 'study', __('Study Materials', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
					new InputField( 'reference', __('Bible reference', 'pulpit') ),
					new TextAreaField('bible_text', __('Bible text', 'pulpit'), 10),
					new InputField( 'notes_header', __('Notes header', 'pulpit') ),
					new TextAreaField('key_points', __('Key points', 'pulpit'), 5),
					new TextAreaField('questions', __('Small group questions', 'pulpit'), 5),
					new RTEField('further_reading', __('Further reading', 'pulpit'), 5),
				]
			),
			new MetaBox('prep', __('Preparation', 'pulpit') , $this->getTypeName(), 'normal', 'high', [
				new TextAreaField( 'prep', __('How to prepare for the service', 'pulpit'), 5 ),
			]),
			new MetaBox('resources', __('Resources', 'pulpit') , $this->getTypeName(), 'normal', 'high', [
				new CheckBoxField( 'cclicense', __('This sermon is released under CC-BY-SA 4.0', 'pulpit') ),
				new InputField( 'handout', __('Handout', 'pulpit') ),
				new CheckBoxField( 'no_handout', __('Don\'t show links to handout', 'pulpit') ),
				new InputField( 'image', __('Title image', 'pulpit') ),
				new InputField( 'preview_image', __('Preview image', 'pulpit') ),
				new InputField( 'image_credits', __('Image credits', 'pulpit') ),
				new InputField( 'audiorecording', __('Audio recording', 'pulpit') ),
				new InputField( 'remote_audio', __('Remote audio file', 'pulpit') ),
				new InputField( 'videorecording', __('Video recording', 'pulpit') ),
			]),
			new MetaBox( 'sync', __('Synchronization', 'pulpit'), $this->getTypeName(), 'normal', 'high', [
					new InputField( 'sync_uid', __('Sync ID', 'pulpit') ),
					new InputField( 'remote_url', __('Remote URL', 'pulpit') ),
					new InputField( 'church', __('Church', 'pulpit') ),
					new InputField( 'church_url', __('Church URL', 'pulpit') ),
				]
			),
            new MetaBox('ebook', __('E-book', 'pulpit') , $this->getTypeName(), 'normal', 'high', [
                new InputField( 'link_amazon', __('Amazon link', 'pulpit') ),
                new InputField( 'link_smashwords', __('Smashwords link', 'pulpit') ),
            ]),
		];
	}


}
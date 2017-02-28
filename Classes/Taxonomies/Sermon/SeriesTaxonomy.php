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


namespace Peregrinus\Pulpit\Taxonomies\Sermon;


use Peregrinus\Pulpit\Taxonomies\AbstractTaxonomy;

/**
 * Class SeriesTaxonomy
 * @package Peregrinus\Pulpit\Taxonomies\Sermon
 */
class SeriesTaxonomy extends AbstractTaxonomy {

	protected $postType = 'sermon';

	/**
	 * SeriesTaxonomy constructor.
	 */
	public function __construct() {
		$this->labels        = [
			'name'                       => __( 'Sermon Series', PEREGRINUS_PULPIT_SLUG ),
			'singular_name'              => __( 'Sermon Series', PEREGRINUS_PULPIT_SLUG ),
			'menu_name'                  => __( 'Sermon Series', PEREGRINUS_PULPIT_SLUG ),
			'search_items'               => __( 'Search sermon series', PEREGRINUS_PULPIT_SLUG ),
			'popular_items'              => __( 'Most frequent sermon series', PEREGRINUS_PULPIT_SLUG ),
			'all_items'                  => __( 'All sermon series', PEREGRINUS_PULPIT_SLUG ),
			'edit_item'                  => __( 'Edit sermon series', PEREGRINUS_PULPIT_SLUG ),
			'update_item'                => __( 'Update sermon series', PEREGRINUS_PULPIT_SLUG ),
			'add_new_item'               => __( 'Add new sermon series', PEREGRINUS_PULPIT_SLUG ),
			'new_item_name'              => __( 'New sermon series name', PEREGRINUS_PULPIT_SLUG ),
			'separate_items_with_commas' => __( 'Separate sermon series with commas', PEREGRINUS_PULPIT_SLUG ),
			'add_or_remove_items'        => __( 'Add or remove sermon series', PEREGRINUS_PULPIT_SLUG ),
			'choose_from_most_used'      => __( 'Choose from most used sermon series', PEREGRINUS_PULPIT_SLUG ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
		];
		$this->configuration = [
			'hierarchical' => false,
			'show_ui'      => true,
			'query_var'    => true,
		];
		parent::__construct();
	}

}
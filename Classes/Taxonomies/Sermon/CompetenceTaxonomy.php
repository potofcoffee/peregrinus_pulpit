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
class CompetenceTaxonomy extends AbstractTaxonomy
{

    protected $postType = 'sermon';

    /**
     * SeriesTaxonomy constructor.
     */
    public function __construct()
    {
        $this->labels = [
            'name' => __('Competences', 'pulpit'),
            'singular_name' => __('Competence', 'pulpit'),
            'menu_name' => __('Competences', 'pulpit'),
            'search_items' => __('Search competences', 'pulpit'),
            'popular_items' => __('Most frequent competences', 'pulpit'),
            'all_items' => __('All competences', 'pulpit'),
            'edit_item' => __('Edit competence', 'pulpit'),
            'update_item' => __('Update competence', 'pulpit'),
            'add_new_item' => __('Add new competence', 'pulpit'),
            'new_item_name' => __('New competence', 'pulpit'),
            'separate_items_with_commas' => __('Separate multiple competences with commas', 'pulpit'),
            'add_or_remove_items' => __('Add or remove competences', 'pulpit'),
            'choose_from_most_used' => __('Choose from most frequent competences', 'pulpit'),
            'parent_item' => null,
            'parent_item_colon' => null,
        ];
        $this->configuration = [
            'hierarchical' => true,
            'show_ui' => true,
            'query_var' => true,
        ];
        parent::__construct();
    }

}
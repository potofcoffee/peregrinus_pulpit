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

namespace Peregrinus\Pulpit\Taxonomies\Song;

use Peregrinus\Pulpit\Debugger;
use Peregrinus\Pulpit\Fields\InputField;
use Peregrinus\Pulpit\Taxonomies\AbstractTaxonomy;

/**
 * Class SeriesTaxonomy
 * @package Peregrinus\Pulpit\Taxonomies\Sermon
 */
class SongbookTaxonomy extends AbstractTaxonomy
{

    protected $postType = 'song';

    /**
     * SeriesTaxonomy constructor.
     */
    public function __construct()
    {
        $this->labels = [
            'name' => __('Songbooks', 'pulpit'),
            'singular_name' => __('Songbook', 'pulpit'),
            'menu_name' => __('Songbook', 'pulpit'),
            'search_items' => __('Search songbooks', 'pulpit'),
            'popular_items' => __('Most frequent songbooks', 'pulpit'),
            'all_items' => __('All songbooks', 'pulpit'),
            'edit_item' => __('Edit songbooks', 'pulpit'),
            'update_item' => __('Update songbook', 'pulpit'),
            'add_new_item' => __('Add new songbook', 'pulpit'),
            'new_item_name' => __('New songbook name', 'pulpit'),
            'separate_items_with_commas' => __('Separate songbooks with commas', 'pulpit'),
            'add_or_remove_items' => __('Add or remove songbooks', 'pulpit'),
            'choose_from_most_used' => __('Choose from most used songbooks', 'pulpit'),
            'parent_item' => null,
            'parent_item_colon' => null,
        ];
        $this->configuration = [
            'hierarchical' => false,
            'show_ui' => true,
            'query_var' => true,
        ];
        parent::__construct();
    }

    protected function addCustomFields()
    {
        return [
            new InputField('abbreviation', __('Abbreviation', 'pulpit')),
        ];
    }

    public function renderCustomFields(object $tag, string $taxonomy)
    {
        parent::renderCustomFields($tag, $taxonomy);
    }

}

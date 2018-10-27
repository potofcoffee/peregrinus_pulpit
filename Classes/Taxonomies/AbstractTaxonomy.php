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

namespace Peregrinus\Pulpit\Taxonomies;

/**
 * Class AbstractTaxonomy
 * Provides basic functionality for a custom taxonomy. All custom taxonomies should extend this class.
 * @package Peregrinus\Pulpit\Taxonomies
 */
class AbstractTaxonomy
{

    public $labels = [];
    protected $configuration = [];
    protected $postType = '';

    public function __construct()
    {
        $this->configuration['labels'] = $this->labels;
        $this->configuration['rewrite'] = $this->getSlug();
    }

    /**
     * Get the slug for this object
     *
     * Normally, the slug will be the key for this object, but this can be overridden by
     * setting the slug_<key> option
     *
     * @return mixed
     */
    protected function getSlug()
    {
        return get_option(PEREGRINUS_PULPIT . '_slug_'.$this->getKey(), $this->getKey());
    }

    /**
     * Get the key for this object
     * @return string
     */
    public function getKey()
    {
        return lcfirst(str_replace('Taxonomy', '', array_pop(explode('\\', get_class($this)))));
    }

    /**
     * Register this taxonomy
     */
    public function register()
    {
        \register_taxonomy(
            $this->getName(),
            PEREGRINUS_PULPIT . '_' . $this->postType,
            $this->configuration
        );
    }

    /**
     * Get the registered name for this taxonomy
     * @return string name
     */
    public function getName()
    {
        return PEREGRINUS_PULPIT . '_' . $this->postType . '_' . $this->getKey();
    }

}

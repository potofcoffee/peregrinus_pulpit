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

/**
 * Class AbstractPostType
 * This class contains the basic functionality for all PostTypes and should be extended in order to add individual
 * configuration and functionality.
 * @package Peregrinus\Pulpit\PostTypes
 */
class AbstractPostType {

	protected $labels = [];
	protected $configuration = [];

	/**
	 * AbstractPostType constructor.
	 */
	public function __construct() {
		$this->configuration['labels']    = $this->labels;
		$this->configuration['menu_icon'] = PEREGRINUS_PULPIT_BASE_URL . 'Resources/Public/Images/PostTypes/' . ucfirst( $this->getKey() ) . '.svg';
		$this->configuration['slug']      = $this->getSlug();
		$this->configuration['rewrite']   = [ 'slug' => $this->getSlug(), 'with_front' => false ];
	}

	/**
	 * Get the key for this PostType
	 * @return string
	 */
	public function getKey() {
		return lcfirst( str_replace( 'PostType', '', array_pop( explode( '\\', get_class( $this ) ) ) ) );
	}

	/**
	 * Get the slug for this PostType
	 *
	 * Normally, the slug will be the key for this PostType, but this can be overridden by
	 * setting the slug_<key> option
	 *
	 * @return mixed
	 */
	protected function getSlug() {
		$defaultSlug = $this->getKey();
		$options     = get_option( PEREGRINUS_PULPIT.'_general' );

		return ( isset( $options[ 'slug_' . $defaultSlug ] ) ? $options[ 'slug_' . $defaultSlug ] : $defaultSlug );
	}

	/**
	 * Register custom columns
	 */
	public function registerCustomColumns() {
	}

	/**
	 * Register this PostType
	 */
	public function register() {
		$res = register_post_type( $this->getTypeName(), $this->configuration );
	}

	/**
	 * Get registered type name
	 * @return string
	 */
	public function getTypeName() {
		return PEREGRINUS_PULPIT . '_' . $this->getKey();
	}

	/**
	 * Add all meta boxes / custom fields for this type
	 */
	public function addMetaBox() {
		foreach ( $this->addCustomFields() as $metaBox ) {
			$metaBox->register();
		}
	}

	/**
	 * Add the custom fields for this post type
	 */
	public function addCustomFields() {
		return [];
	}


}
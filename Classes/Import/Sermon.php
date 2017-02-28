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


namespace Peregrinus\Pulpit\Import;


use Peregrinus\Pulpit\PostTypes\SermonPostType;
use Peregrinus\Pulpit\Taxonomies\Sermon\PreacherTaxonomy;
use Peregrinus\Pulpit\Taxonomies\Sermon\SeriesTaxonomy;

require_once( ABSPATH . 'wp-admin/includes/image.php' );

class Sermon {

	protected $host = '';
	protected $rawData = [];
	protected $data = [];

	public function __construct( $rawData, $host ) {
		$this->setRawData( $rawData );
		$this->setHost( $host );
	}

	/**
	 * Convert raw data to usable data
	 */
	public function convert() {
		$this->setData( $this->getRawData()['sermon'] );

		// series:
		$this->data['series'] = [];
		foreach ( $this->getRawData()['series'] as $series ) {
			$this->data['series'][] = $series['title'];
		}

		// preacher
		$this->data['preacher'] = $this->getRawData()['preacher']['name'];

		// preached
		$this->data['preached'] = new \DateTime( $this->getRawData()['preached']['date'] . $this->getRawData()['preached']['timezone'] );

		// url
		$this->data['url'] = $this->getRawData()['url'];

		// syncuid:
		$this->data['syncuid'] = $this->getSyncUid();
	}

	/**
	 * @return array
	 */
	public function getRawData() {
		return $this->rawData;
	}

	/**
	 * @param array $rawData
	 */
	public function setRawData( $rawData ) {
		$this->rawData = $rawData;
	}

	public function getSyncUid() {
		return $this->getHost() . ':' . $this->getData()['uid'];
	}

	/**
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * @param string $host
	 */
	public function setHost( $host ) {
		$this->host = $host;
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param array $data
	 */
	public function setData( $data ) {
		$this->data = $data;
	}

	/**
	 * Create a new post for this sermon
	 * @return int|\WP_Error Post id or WP_Error
	 */
	public function post() {
		$postData = $this->getPostData();
		if ( isset( $postData['ID'] ) ) {
			return $this->updatePost( $postData );
		} else {
			return $this->createPost( $postData );
		}

		return $id;
	}

	/**
	 * Get Post data
	 * @return array Post data
	 */
	public function getPostData() {
		__log( $this, 'Building post data...' );
		$data     = $this->getData();
		$postType = new SermonPostType();

		$preacherTaxonomy = new PreacherTaxonomy();
		$seriesTaxonomy   = new SeriesTaxonomy();

		$postData = [
			'post_type'     => $postType->getTypeName(),
			'post_status'   => 'publish',
			'post_date'     => $data['preached']->format( 'Y-m-d H:i:s' ),
			'post_date_gmt' => $data['preached']->format( 'Y-m-d H:i:s' ),
			'post_content'  => $data['description'],
			'post_title'    => $data['title'],
			'post_excerpt'  => $data['description'],
			'meta_input'    => [
				'sync_uid' => $this->getSyncUid(),
			],
			'tax_input'     => [
				$preacherTaxonomy->getName() => [ $data['preacher'] ],
				$seriesTaxonomy->getName()   => $data['series'],
			],
		];

		foreach (
			[
				'subtitle'       => 'subtitle',
				'reference'      => 'reference',
				'bibleText'      => 'bible_text',
				'notesHeader'    => 'notes_header',
				'keypoints'      => 'key_points',
				'questions'      => 'questions',
				'furtherReading' => 'further_reading',
				'prep'           => 'prep',
				'cclicense'      => 'cclicense',
				'handout'        => 'handout',
				'noHandout'      => 'no_handout',
				'image'          => 'image',
				'imagePreview'   => 'preview_image',
				'imageSource'    => 'image_credits',
				'audiorecording' => 'audiorecording',
				'videorecording' => 'videorecording',
				'remoteAudio'    => 'remote_audio',
				'remote_url'     => 'url',

			] as $t3Key => $wpKey
		) {
			$postData['meta_input'][ $wpKey ] = $data[ $t3Key ];
		}

		// update existing post?
		if ( ( $id = $this->exists() ) !== false ) {
			$postData['ID'] = $id;
		}

		return $postData;
	}

	/**
	 * Find if this sermon already exists
	 * @return int|false Post id, if one exists
	 */
	public function exists() {
		global $wpdb;
		$postType = new SermonPostType();
		$query    = "
			SELECT *
			FROM {$wpdb->prefix}posts
			INNER JOIN {$wpdb->prefix}postmeta m1
			  ON ( {$wpdb->prefix}posts.ID = m1.post_id )
			WHERE
			{$wpdb->prefix}posts.post_type = '{$postType->getTypeName()}'
			AND {$wpdb->prefix}posts.post_status = 'publish'
			AND ( m1.meta_key = 'sync_uid' AND m1.meta_value = '{$this->getSyncUid()}' )
			GROUP BY {$wpdb->prefix}posts.ID
			ORDER BY {$wpdb->prefix}posts.post_date
			DESC;
			";

		$results = $wpdb->get_results( $query );
		if ( count( $results ) ) {
			$id = $results[0]->ID;
			__log( $this, 'Post already exists with ID ' . $id );

			return $id;
		} else {
			return false;
		}
	}

	/**
	 * Update existing post
	 *
	 * @param array $postData Post data
	 *
	 * @return int|\WP_Error Post id or WP_Error
	 */
	protected function updatePost( $postData ) {
		$id = wp_update_post( $postData, true );
		__log( $this, 'Updated post #' . print_r( $id, 1 ) );
		$this->attachImageFile( $id, $this->data['image'] );

		return $id;
	}

	/**
	 * Attach image file
	 *
	 * @param int $postId Post id
	 * @param string $imageFile Path to image file
	 */
	protected function attachImageFile( $postId, $imageFile ) {
		$uploadPath = wp_upload_dir();
		if ( ! file_exists( $uploadPath['path'] . '/' . basename( $imageFile ) ) ) {
			$imageFile  = $this->downloadFile( $imageFile, $uploadPath['path'] . '/' );
			$fileType   = wp_check_filetype( $imageFile, null )['type'];
			$guid       = $uploadPath['url'] . '/' . basename( $imageFile );
			$attachment = [
				'guid'           => $guid,
				'post_status'    => 'inherit',
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $imageFile ) ),
				'post_content'   => $imageFile,
				'post_mime_type' => $fileType,
			];

			// check if this attachment already exists:
			$media        = get_attached_media( 'image', $postId );
			$attachmentId = 0;
			foreach ( $media as $item ) {
				if ( $item->guid == $guid ) {
					$attachmentId = $item->ID;
					__log( $this, 'Attachment already exists with ID ', $attachmentId );
				}
			}
			// ..if not, create attachment
			if ( ! $attachmentId ) {
				$attachmentId = \wp_insert_attachment( $attachment, $imageFile, $postId );
				__log( $this, 'Created new attachment: Image ' . $imageFile . ' with attachment ID ' . $attachmentId );
			}

			// update all attachment data
			$attachmentData = \wp_generate_attachment_metadata( $attachmentId, $imageFile );
			\wp_update_attachment_metadata( $attachmentId, $attachmentData );
			\set_post_thumbnail( $postId, $attachmentId );

		} else {
			__log( $this, 'Skipped processing existing image.' );
		}
	}

	/**
	 * Download a remote file
	 *
	 * @param string $source Source path
	 * @param string $target Folder
	 */
	public function downloadFile( $source, $target ) {
		if ( is_dir( $target ) ) {
			$target = $target . '/' . pathinfo( $source, PATHINFO_BASENAME );
		}
		file_put_contents( $target, file_get_contents( $source ) );

		return $target;
	}

	/**
	 * @param array $postData Post data
	 *
	 * @return int|\WP_Error Post id or WP_Error
	 */
	protected function createPost( $postData ) {
		$id = wp_insert_post( $postData );
		$this->attachImageFile( $id, $this->data['image'] );
		__log( $this, 'Inserted as new post #' . print_r( $id, 1 ) );

		return $id;
	}

	/**
	 * Get attachments to an existing post
	 *
	 * @param int $postId Post id
	 * @param string $type Optional: Mime type
	 *
	 * @return array Attachments
	 */
	protected function getAttachments( $postId, $type = '' ) {
		global $wpdb;
		$query = "
			SELECT *
			FROM {$wpdb->prefix}posts
			WHERE
			{$wpdb->prefix}posts.post_type = 'attachment'
			AND {$wpdb->prefix}posts.post_status = 'publish'
			AND {$wpdb->prefix}posts.post_parent = {$postId}
			ORDER BY {$wpdb->prefix}posts.post_date
			DESC;
			";

		__log( $this, 'Attachment query', $query );
		$results = $wpdb->get_results( $query );
		__log( $this, 'Results', $results );

	}


}
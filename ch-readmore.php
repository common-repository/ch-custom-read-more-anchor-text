<?php

/*
Plugin Name: CH Custom Read More Anchor Text
Plugin URI: https://haensel.pro/plugins/ch-readmore/
Description: Customize your read more link anchor texts for each post
Author: Christian Hänsel
Version: 1.0
Author URI: https://haensel.pro
Text Domain: ch-readmore
License:     GPLv2

CH Custom Read More Anchor Text is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

It is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this spftware.
*/

class ChReadmore {


	/**
	 * ChReadmore constructor.
	 */
	public function __construct() {
		add_action( 'the_content_more_link', [ $this, 'ch_read_more_edit' ], 10, 2 );
		add_action( 'add_meta_boxes', [ $this, 'add_custom_box' ] );
		add_action( 'save_post', [ $this, 'save_postdata' ] );
	}

	/**
     * Saving the data
     *
	 * @param $post_id
	 */
	public function save_postdata( $post_id ) {
		if ( array_key_exists( 'ch_readmore_linktext_save', $_POST ) ) {
			update_post_meta(
				$post_id,
				'_ch_readmore_linktext_save',
				sanitize_text_field($_POST['ch_readmore_linktext_save'])
			);
		}
	}


	/**
	 * Adding the meta box to the admin edit post screen
	 */
	public function add_custom_box() {
		$screens = [ 'post', 'wporg_cpt' ];
		foreach ( $screens as $screen ) {
			add_meta_box(
				'ch_readmore_linktext',           // Unique ID
				'Read More Link Text',  // Box title
				[ $this, 'ch_readmore_box_html' ],  // Content callback, must be of type callable
				$screen,                   // Post type,
				"side",
				"high"
			);
		}
	}

	/**
     * The HTML for the admin meta box
     *
	 * @param $post
	 */
	public function ch_readmore_box_html( $post ) {
		$value = get_post_meta( $post->ID, '_ch_readmore_linktext_save', true );
		?>
        <p>Insert the text that should be shown as the "read more" link text.</p>
        <input style="width:100%" type="text" id="ch_readmore_field" name="ch_readmore_linktext_save" value="<?= $value ?>">
		<?php
	}


	/**
     * Outputting the customized link anchor text
     *
	 * @param $link
	 * @param $text
	 *
	 * @return string|string[]|null
	 */
	public function ch_read_more_edit( $link, $text ) {
		global $post;
		$val = get_post_meta( $post->ID, '_ch_readmore_linktext_save', true );
		if ( ! is_null( $val ) && strlen( $val ) > 0 ) {

			$link = preg_replace( '#(<a.*?>)[^>]*(</a>)#s', $val . '${2}', $link );
			$link = '<a class="more-link" href="' . get_permalink() . '">' . $val .  '</a>';
		}

		return $link;
	}
}

$readmore = new ChReadmore();
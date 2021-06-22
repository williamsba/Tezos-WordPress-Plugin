<?php

/**
 * Registers the NFT custom post type 
 *
 *
 * @link       https://strangework.com
 * @since      0.1
 *
 * @package    Hen
 * @subpackage Hen/includes
 */

class Hen_CPTs {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1
	 */
	public function hen_register_nft_cpt() {

		/**
		 * Post Type: NFTs.
		 */

		$labels = [
			"name" => __( "NFTs", "hen" ),
			"singular_name" => __( "NFT", "hen" ),
		];

		$args = [
			"label" => __( "NFTs", "hen" ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"has_archive" => false,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"delete_with_user" => false,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => [ "slug" => "nft", "with_front" => true ],
			"query_var" => true,
			"supports" => [ "title", "editor", "thumbnail", "custom-fields", "comments", "revisions", "author" ],
			"show_in_graphql" => false,
		];

		register_post_type( "nft", $args );

	}

}
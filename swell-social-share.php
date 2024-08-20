<?php
/**
 * Plugin Name:     Swell Social Share
 * Plugin URI:      https://swellinc.co/
 * Description:     Nothing
 * Author:          tboggia
 * Author URI:      https://swellinc.co
 * Text Domain:     swell-social-share
 * Domain Path:     /languages
 * Version:         0.1.0
 * Text Domain:     swellsocial
 *
 * @package         Swell_Social_Share
 */

define('Swell_Social_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
include_once(Swell_Social_PLUGIN_DIR . 'includes/class-swell-social-template-loader.php');

add_action('add_sharer_script', function () {
    wp_enqueue_script('sharer', 'https://cdn.jsdelivr.net/npm/sharer.js@latest/sharer.min.js');
});

/**
 * Render Shortcode HTML
 */
add_shortcode( 'swell-social', function ( $atts ) {
  do_action('add_sharer_script');

	$attributes = shortcode_atts( array(
		'platforms' => [
			'x' => [
				'name' => 'x',
				'copy' => 'test',
			]
		],
		'options'             => [
			'title'    => ['x', 'email', 'whatsapp', 'reddit'],
			'hashtags' => ['x', 'facebook'],
			'hashtag'  => ['facebook'],
			'subject'  => ['email'],
			'to'       => ['email'],
			'web'      => ['whatsapp'],
			'via'      => ['x'],
		],
		'hashtags' => [''],
	), $atts );

	ob_start();
	$swell_social_template_loader = new Swell_Social_Template_Loader();
	
	$swell_social_template_loader
    ->set_template_data( $attributes, 'socials' )
		->get_template_part( 'swell-social-share-template' );

	return ob_get_clean();
});


/**
 * Add fields to post type post
 */


/**
		* Get font-awesome icons.
*/
function SwellSocialGetFAIcon($slug) {
		if(!$slug) return false;
		switch ($slug) {
				case 'x':
						return "fab fa-x-twitter";
				case 'facebook':
						return "fab fa-facebook";
				case 'email':
						return "fa fa-envelope";
				default:
						return "fab fa-$slug";
		};
}
 
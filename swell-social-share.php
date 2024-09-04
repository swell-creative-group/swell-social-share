<?php
/**
 * Plugin Name:     Swell Social Share
 * Plugin URI:      https://raw.githubusercontent.com/swell-creative-group/swell-social-share/master/site/
 * Description:     Swell, Inc. plugin to add social share functionality to all websites.
 * Author:          swell-creative-group
 * Author URI:      https://swellinc.co
 * Text Domain:     swell-social-share
 * Update URI:      https://github.com/swell-creative-group/swell-social-share/
 * Domain Path:     /languages
 * Version:         1.2.0
 * Text Domain:     swellsocial
 *
 * @package         Swell_Social_Share
 */

define('Swell_Social_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

require_once(Swell_Social_PLUGIN_DIR . 'includes/GitHubUpdater.php');
include_once(Swell_Social_PLUGIN_DIR . 'includes/class-swell-social-template-loader.php');
include_once(Swell_Social_PLUGIN_DIR . 'includes/class-swell-social-template-loader.php');


add_action('swellsocial_add_sharer_script', function () {
	wp_enqueue_script('sharer', 'https://cdn.jsdelivr.net/npm/sharer.js@latest/sharer.min.js');
});

function shortcode_footer_func() {
  wp_enqueue_style( 'swellsocial', plugin_dir_url(__FILE__) . 'resources/styles/plugin.css' );
}

add_action( 'wp_footer', 'shortcode_footer_func' );

/**
 * Render Shortcode HTML
 */
add_shortcode( 'social-share', function ( $atts ) {
	global $post;

  do_action('swellsocial_add_sharer_script');
	// Use get_post_meta to retrieve an existing value from the database.
	$hashtags 		 = get_post_meta( $post->ID, '_swellsocial_field_hashtags', true );
	$facebook_show = get_post_meta( $post->ID, '_swellsocial_facebook_show', true );
	$linkedin_show = get_post_meta( $post->ID, '_swellsocial_linkedin_show', true );
	$x_show 			 = get_post_meta( $post->ID, '_swellsocial_x_show', true );
	$x_copy 			 = get_post_meta( $post->ID, '_swellsocial_x_copy', true );
	$x_via 			 	 = get_post_meta( $post->ID, '_swellsocial_x_via', true );
	$email_show 	 = get_post_meta( $post->ID, '_swellsocial_email_show', true );
	$email_copy 	 = get_post_meta( $post->ID, '_swellsocial_email_copy', true );
	$whatsapp_show = get_post_meta( $post->ID, '_swellsocial_whatsapp_show', true );
	$whatsapp_copy = get_post_meta( $post->ID, '_swellsocial_whatsapp_copy', true );
	$reddit_show   = get_post_meta( $post->ID, '_swellsocial_reddit_show', true );
	$reddit_copy 	 = get_post_meta( $post->ID, '_swellsocial_reddit_copy', true );
	$platforms = [];
	
	if ($facebook_show) $platforms["facebook"] = [
		"name" => "facebook",
	];
	if ($linkedin_show) $platforms["linkedin"] = [
		"name" => "linkedin",
	];
	if ($x_show) $platforms["x"] = [
		"name" => "x",
		"copy" => $x_copy,
		"via" => $x_via,
	];
	if ($email_show) $platforms["email"] = [
		"name" => "email",
		"copy" => $email_copy,
	];
	if ($whatsapp_show) $platforms["whatsapp"] = [
		"name" => "whatsapp",
		"copy" => $whatsapp_copy,
	];
	if ($reddit_show) $platforms["reddit"] = [
		"name" => "reddit",
		"copy" => $reddit_copy,
	];

	$attributes = shortcode_atts( [
		'platforms' => $platforms,
		'options'             => [
			'title'    => ['x', 'email', 'whatsapp', 'reddit'],
			'hashtags' => ['x', 'facebook'],
			'hashtag'  => ['facebook'],
			'subject'  => ['email'],
			'to'       => ['email'],
			'web'      => ['whatsapp'],
			'via'      => ['x'],
		],
		'hashtags' => explode(', ', $hashtags),
	], $atts );

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
if ( is_admin() ) {
	add_action('admin_init', 'swellsocial_add_metabox_post_sidebar');
	add_action('save_post', 'swellsocial_save_metabox_post_sidebar');
}

/*
 * Funtion to add a meta box to enable/disable the posts.
 */
function swellsocial_add_metabox_post_sidebar() {
	$active_post_types = get_option( 'swellsocial_options' );
  add_meta_box(
		"social_share", 
		__("Social share", 'swellsocial'), 
		"swellsocial_social_fields", 
		$active_post_types, 
		"side", 
		"low"
	);
}

function swellsocial_social_fields() {
		global $post;
  	// Add an nonce field so we can check for it later.
		wp_nonce_field( 'swellsocial_inner_custom_box', 'swellsocial_inner_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$hashtags 		 = get_post_meta( $post->ID, '_swellsocial_field_hashtags', true );
		$facebook_show = get_post_meta( $post->ID, '_swellsocial_facebook_show', true );
		$linkedin_show = get_post_meta( $post->ID, '_swellsocial_linkedin_show', true );
		$x_show 			 = get_post_meta( $post->ID, '_swellsocial_x_show', true );
		$reddit_copy 	 = get_post_meta( $post->ID, '_swellsocial_reddit_copy', true );
		$x_copy 			 = get_post_meta( $post->ID, '_swellsocial_x_copy', true );
		$x_via 				 = get_post_meta( $post->ID, '_swellsocial_x_via', true );
		$email_show 	 = get_post_meta( $post->ID, '_swellsocial_email_show', true );
		$email_copy 	 = get_post_meta( $post->ID, '_swellsocial_email_copy', true );
		$whatsapp_show = get_post_meta( $post->ID, '_swellsocial_whatsapp_show', true );
		$whatsapp_copy = get_post_meta( $post->ID, '_swellsocial_whatsapp_copy', true );
		$reddit_show   = get_post_meta( $post->ID, '_swellsocial_reddit_show', true );

		// Display the form, using the current value.
		?>
		<style><?php include Swell_Social_PLUGIN_DIR . 'resources/styles/admin.css'; ?></style>
		<p>Use the shortcode <code>[social-share]</code> to add social share icons to this page.</p>
		<div class="inside-field-wrapper">
			<label for="swellsocial_facebook_show">
				<input type="checkbox" <?php echo $facebook_show ? "checked" : "" ; ?> id="swellsocial_facebook_show" name="swellsocial_facebook_show">
				<?php _e("Show Facebook?", "swellsocial"); ?>
			</label>
		</div>

		<!-- Linkedin -->
		<div class="inside-field-wrapper">
			<label for="swellsocial_linkedin_show">
				<input type="checkbox" <?php echo $linkedin_show ? "checked" : "" ; ?> id="swellsocial_linkedin_show" name="swellsocial_linkedin_show">
				<?php _e("Show LinkedIn?", "swellsocial"); ?>
			</label>
		</div>

		<!-- X -->
		<div class="inside-field-wrapper">
			<label for="swellsocial_x_show">
				<input type="checkbox" <?php echo $x_show ? "checked" : "" ; ?> id="swellsocial_x_show" name="swellsocial_x_show">
				<?php _e("Show X?", "swellsocial"); ?>
			</label>
			<div id="swellsocial_x_copy_wrapper" class="inside-field-wrapper--copy <?php echo ($x_show ? "" : "hidden") ; ?>">
				<div>
					<label for="swellsocial_x_copy" class="block" id="swellsocial_x_copy_label"><?php _e("X post", "swellsocial"); ?></label>
					<textarea id="swellsocial_x_copy" class="block" name="swellsocial_x_copy" rows="3" cols="25" aria-labelledby="swellsocial_x_copy_label"><?php echo $x_copy ?></textarea>
				</div>
				<div>
					<label for="swellsocial_x_via"><?php _e( 'X account to mention?', 'swellsocial' ); ?></label>
					<input type="text" id="swellsocial_x_via" name="swellsocial_x_via" value="<?php echo  $x_via ? '@' . esc_attr( $x_via ) : ""; ?>" size="25" />
				</div>
			</div>
		</div>
		

		<!-- Email -->
		<div class="inside-field-wrapper">
			<label for="swellsocial_email_show">
				<input type="checkbox" <?php echo $email_show ? "checked" : "" ; ?> id="swellsocial_email_show" name="swellsocial_email_show">
				<?php _e("Show Email?", "swellsocial"); ?>
			</label>
			<div id="swellsocial_email_copy_wrapper" class="inside-field-wrapper--copy <?php echo ($email_show ? "" : "hidden") ; ?>">
				<label for="swellsocial_email_copy" class="block" id="swellsocial_email_copy_label"><?php _e("Email subject", "swellsocial"); ?></label>
				<textarea id="swellsocial_email_copy" class="block" name="swellsocial_email_copy" rows="3" cols="25" aria-labelledby="swellsocial_email_copy_label"><?php echo $email_copy ?></textarea>
			</div>
		</div>

		<!-- Whatsapp -->
		<div class="inside-field-wrapper">
			<label for="swellsocial_whatsapp_show">
				<input type="checkbox" <?php echo $whatsapp_show ? "checked" : "" ; ?> id="swellsocial_whatsapp_show" name="swellsocial_whatsapp_show">
				<?php _e("Show Whatsapp?", "swellsocial"); ?>
			</label>
			<div id="swellsocial_whatsapp_copy_wrapper" class="inside-field-wrapper--copy <?php echo ($whatsapp_show ? "" : "hidden") ; ?>">
				<label for="swellsocial_whatsapp_copy" class="block" id="swellsocial_whatsapp_copy_label"><?php _e("Whatsapp content", "swellsocial"); ?></label>
				<textarea id="swellsocial_whatsapp_copy" class="block" name="swellsocial_whatsapp_copy" rows="3" cols="25" aria-labelledby="swellsocial_whatsapp_copy_label"><?php echo $whatsapp_copy ?></textarea>
			</div>
		</div>

		<!-- Reddit -->
		<div class="inside-field-wrapper">
			<label for="swellsocial_reddit_show">
				<input type="checkbox" <?php echo $reddit_show ? "checked" : "" ; ?> id="swellsocial_reddit_show" name="swellsocial_reddit_show">
				<?php _e("Show Reddit?", "swellsocial"); ?>
			</label>
			<div id="swellsocial_reddit_copy_wrapper" class="inside-field-wrapper--copy <?php echo ($reddit_show ? "" : "hidden") ; ?>">
				<label for="swellsocial_reddit_copy" class="block" id="swellsocial_reddit_copy_label"><?php _e("Reddit title", "swellsocial"); ?></label>
				<textarea id="swellsocial_reddit_copy" class="block" name="swellsocial_reddit_copy" rows="3" cols="25" aria-labelledby="swellsocial_reddit_copy_label"><?php echo $reddit_copy ?></textarea>
			</div>
		</div>

		<!-- Hashtags -->
		<div class="inside-field-wrapper">
			<label for="swellsocial_field_hashtags"><?php _e( 'Hashtags', 'swellsocial' ); ?></label>
			<input type="text" id="swellsocial_field_hashtags" name="swellsocial_field_hashtags" value="<?php echo esc_attr( $hashtags ); ?>" size="25" />
		</div>

		<script>
			// Function to toggle visibility of textarea based on checkbox status
			function toggleTextarea(checkboxId, textareaId) {
				const checkbox = document.getElementById(checkboxId);
				const textarea = document.getElementById(textareaId);
				
				if (checkbox && textarea) {
					if ( checkbox.checked ) {
						textarea.classList.remove('hidden');
					} else textarea.classList.add('hidden');
					
					// Event listener to toggle textarea visibility dynamically
					checkbox.addEventListener('change', function() {
						if ( checkbox.checked ) {
							textarea.classList.remove('hidden');
						} else textarea.classList.add('hidden');
					});
				}
			}
			function hashtagizeField(e) {
				const field = e.srcElement;
				if (!field) return false;
				
				field.value = field.value.split(',').filter((tag) => tag.length > 0).flatMap((tag) => hashtagize(tag)).join(', ');
				console.log(field.value);
			}

			function hashtagize(text) {
				if (typeof text !== 'string') return false;

				return text.trim().replace(/[^a-zA-Z0-9\s]/g, '').replace(/(?:^\w|[A-Z]|\b\w)/g, function(word, index) {
						return index === 0 ? word.toLowerCase() : word.toUpperCase();
					}).replace(/\s+/g, '');
			}

			const hashtagsField = document.getElementById('swellsocial_field_hashtags');

			hashtagsField.addEventListener('focusout', hashtagizeField)


			// Initialize visibility of textareas based on checkbox states
			toggleTextarea('swellsocial_email_show', 'swellsocial_email_copy_wrapper');
			toggleTextarea('swellsocial_whatsapp_show', 'swellsocial_whatsapp_copy_wrapper');
			toggleTextarea('swellsocial_x_show', 'swellsocial_x_copy_wrapper');
			toggleTextarea('swellsocial_x_show', 'swellsocial_x_copy_wrapper');
		</script>

		<?php
}

function swellsocial_admin_scripts() {
	
}

/*
 * Save the Enable/Disable sidebar meta box value
 */
function swellsocial_save_metabox_post_sidebar($post_id) {
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['swellsocial_inner_custom_box_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['swellsocial_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'swellsocial_inner_custom_box' ) ) {
			return $post_id;
		}

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		/* OK, it's safe for us to save the data now. */
		// // Sanitize the user input.
		$hashtags = sanitize_text_field( $_POST['swellsocial_field_hashtags'] );
		$facebook_show = isset( $_POST['swellsocial_facebook_show'] ) ? $_POST['swellsocial_facebook_show'] : null;
		$linkedin_show = isset( $_POST['swellsocial_linkedin_show'] ) ? 1 : 0;
		$x_show = isset( $_POST['swellsocial_x_show'] ) ? 1 : 0;
		$x_copy = sanitize_text_field( $_POST['swellsocial_x_copy'] );
		$x_copy = sanitize_text_field( $_POST['swellsocial_x_copy'] );
		$x_via = sanitize_text_field( $_POST['swellsocial_x_via'] );
		$email_show = isset( $_POST['swellsocial_email_show'] ) ? 1 : 0;
		$email_copy = sanitize_text_field( $_POST['swellsocial_email_copy'] );
		$whatsapp_show = isset( $_POST['swellsocial_whatsapp_show'] ) ? 1 : 0;
		$whatsapp_copy = sanitize_text_field( $_POST['swellsocial_whatsapp_copy'] );
		$reddit_show = isset( $_POST['swellsocial_reddit_show'] ) ? 1 : 0;

		// Update the meta field.
		update_post_meta( $post_id, '_swellsocial_field_hashtags', $hashtags );
		update_post_meta( $post_id, '_swellsocial_facebook_show', $facebook_show );
		update_post_meta( $post_id, '_swellsocial_linkedin_show', $linkedin_show );
		update_post_meta( $post_id, '_swellsocial_x_show', $x_show );
		update_post_meta( $post_id, '_swellsocial_x_copy', $x_copy );
		update_post_meta( $post_id, '_swellsocial_x_copy', $x_copy );
		update_post_meta( $post_id, '_swellsocial_x_via', str_replace('@','', $x_via) );
		update_post_meta( $post_id, '_swellsocial_email_show', $email_show );
		update_post_meta( $post_id, '_swellsocial_email_copy', $email_copy );
		update_post_meta( $post_id, '_swellsocial_whatsapp_show', $whatsapp_show );
		update_post_meta( $post_id, '_swellsocial_whatsapp_copy', $whatsapp_copy );
		update_post_meta( $post_id, '_swellsocial_reddit_show', $reddit_show );
}


/**
		* Get font-awesome icons.
*/
function SwellSocialGetFAIcon($slug) {
		if(!$slug) return false;
		$path = Swell_Social_PLUGIN_DIR . 'resources/fontawesome/';
		switch ($slug) {
				case 'x':
						$path .= "brands/x-twitter.svg";
						break;
				case 'facebook':
						$path .= "brands/facebook.svg";
						break;
				case 'email':
						$path .= "regular/envelope.svg";
						break;
				default:
						$path .= "brands/$slug.svg";
		};
		return file_get_contents($path);
}

$updater = new GitHubUpdater(__FILE__);
$updater->add();

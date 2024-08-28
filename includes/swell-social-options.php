<?php 

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function swellsocial_settings_init() {

	// Register a new setting for "swellsocial" page.
	register_setting( 'swellsocial', 'swellsocial_options', ['type' => 'array', 'default' => [
		'post_types' => [
			'page'
		]
	]]);

	// Register a new section in the "swellsocial" page.
	add_settings_section(
		'swellsocial_section_developers',
		__( 'Post types to enable the shortcode on', 'swellsocial' ), 'swellsocial_section_developers_callback',
		'swellsocial'
	);
	
	// Register a new field in the "swellsocial_section_developers" section, inside the "swellsocial" page.
	
	add_settings_field(
		'swellsocial_field_post_types', // As of WP 4.6 this value is used only internally.
														// Use $args' label_for to populate the id inside the callback.
		__( 'Post Types', 'swellsocial' ),
		'swellsocial_field_post_types_cb',
		'swellsocial',
		'swellsocial_section_developers',
		// array(
			// 'label_for'         => 'swellsocial_field_post_types',
			// 'class'             => 'swellsocial_row',
		// )
	);
}

/**
 * Register our swellsocial_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'swellsocial_settings_init' );


/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function swellsocial_section_developers_callback( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Toggle post types where you would like to see the social share options available in the sidebar', 'swellsocial' ); ?></p>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php _e( 'Use the shortcode <code>[social-share]</code> to add social share icons to this page.', 'swellsocial' ); ?></p>
	<?php
}

/**
 * Post type field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function swellsocial_field_post_types_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'swellsocial_options' );

	$available_post_types = get_post_types([
		'public'=> true,
	], 'names');
	if ( isset($available_post_types['attachment']) ) unset($available_post_types['attachment']);

	$active_values = [];
	if (isset($options['post_types']) && ! empty($options['post_types'])) {
			$active_values = $options['post_types'];
	}
	
	$html = '<div class="inside-field-wrapper">';
	foreach ( $available_post_types as $post_type ) {
		$html .= '<p><label for="swellsocial_options_' . $post_type . '">';
		$html .= '<input type="checkbox" ' 
			. ( in_array( $post_type, $active_values ) ? "checked" : "" ) 
			. ' id="swellsocial_options_' . $post_type 
			. '" name="swellsocial_options[post_types][]"
			value=' . $post_type . '>' . ucwords($post_type) . '</label></p>';
	}
	$html .= '</div>';

	echo $html;
}

/**
 * Add the top level menu page.
 */
function swellsocial_options_page() {
	add_submenu_page(
		'options-general.php',
		__('Social Share Options', 'swellsocial'),
		__('Social Share', 'swellsocial'),
		'manage_options',
		'swellsocial',
		'swellsocial_options_page_html',
	);
}


/**
 * Register our swellsocial_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'swellsocial_options_page' );


/**
 * Top level menu callback function
 */
function swellsocial_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'swellsocial_messages', 'swellsocial_message', __( 'Settings Saved', 'swellsocial' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'swellsocial_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "swellsocial"
			settings_fields( 'swellsocial' );
			// output setting sections and their fields
			// (sections are registered for "swellsocial", each field is registered to a specific section)
			do_settings_sections( 'swellsocial' );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}

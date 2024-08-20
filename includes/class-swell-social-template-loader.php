<?php
/**
 * Swell Social
 *
 * @package   Swell_Social
 * @license   GPL-2.0+
 */

if ( ! class_exists( 'Gamajo_Template_Loader' ) ) {
  require plugin_dir_path( __FILE__ ) . 'class-gamajo-template-loader.php';
}

/**
 * Template loader for Swell Social.
 *
 * Only need to specify class properties here.
 *
 * @package Swell_Social
 */
class Swell_Social_Template_Loader extends Gamajo_Template_Loader {
  /**
   * Prefix for filter names.
   *
   * @since 1.0.0
   *
   * @var string
   */
  protected $filter_prefix = 'Swell_Social';

  /**
   * Directory name where custom templates for this plugin should be found in the theme.
   *
   * @since 1.0.0
   *
   * @var string
   */
  protected $theme_template_directory = 'resources';

  /**
   * Reference to the root directory path of this plugin.
   *
   * Can either be a defined constant, or a relative reference from where the subclass lives.
   *
   * In this case, `Swell_Social_PLUGIN_DIR` would be defined in the root plugin file as:
   *
   * ~~~
   * define( 'Swell_Social_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
   * ~~~
   *
   * @since 1.0.0
   *
   * @var string
   */
  protected $plugin_directory = Swell_Social_PLUGIN_DIR;

  /**
   * Directory name where templates are found in this plugin.
   *
   * Can either be a defined constant, or a relative reference from where the subclass lives.
   *
   * e.g. 'templates' or 'includes/templates', etc.
   *
   * @since 1.1.0
   *
   * @var string
   */
  protected $plugin_template_directory = 'resources';
}
<?php
/**
 * Plugin Name: Teamized Integration
 * Plugin URI: https://github.com/rafaelurben/wp-teamized
 * Description: WordPress plugin for integrating with Teamized.
 * Version: 0.0.0
 * Author: Rafael Urben
 * Author URI: https://github.com/rafaelurben
 * License: GPLv3
 * Text Domain: wp-teamized
 * GitHub Plugin URI: https://github.com/rafaelurben/wp-teamized
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include settings page functionality
require_once plugin_dir_path( __FILE__ ) . 'includes/settings-page.php';

// Include block registration and rendering
require_once plugin_dir_path( __FILE__ ) . 'includes/blocks/register.php';

/**
 * Add settings link to plugin action links
 *
 * @param array $links Existing plugin action links.
 * @return array Modified plugin action links.
 */
function teamized_add_settings_link( $links ) {
    $settings_link = sprintf(
        '<a href="%s">%s</a>',
        admin_url( 'options-general.php?page=teamized-settings' ),
        esc_html__( 'Settings', 'wp-teamized' )
    );
    array_unshift( $links, $settings_link );
    return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'teamized_add_settings_link' );

/**
 * Clean up on plugin deactivation
 */
function teamized_deactivate() {
    // Delete all teamized transients
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_teamized_api_%' OR option_name LIKE '_transient_timeout_teamized_api_%'" );
}

register_deactivation_hook( __FILE__, 'teamized_deactivate' );

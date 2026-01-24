<?php
/**
 * Plugin Name: Teamized Integration
 * Description: WordPress plugin for integration with Teamized.
 * Version: 1.0.0
 * Author: Rafael Urben
 * Text Domain: wp-teamized
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add settings page to WordPress admin menu
 */
function teamized_add_settings_page() {
    add_options_page(
        'Teamized Integration',           // Page title
        'Teamized',                       // Menu title
        'manage_options',                 // Capability
        'teamized-settings',              // Menu slug
        'teamized_render_settings_page'   // Callback function
    );
}
add_action('admin_menu', 'teamized_add_settings_page');

/**
 * Render the settings page
 */
function teamized_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Teamized integration</h1>
    </div>
    <?php
}

/**
 * Register the teamized_club_members shortcode
 */
function teamized_club_members_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(
        array(
            'api_url' => '',
        ),
        $atts,
        'teamized_club_members'
    );
    
    // Return the output
    return 'Club members';
}
add_shortcode('teamized_club_members', 'teamized_club_members_shortcode');

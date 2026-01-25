<?php
/**
 * Settings page functionality
 *
 * @package Teamized
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add settings page to WordPress admin menu
 */
function teamized_add_settings_page() {
    add_options_page(
            'Teamized Integration',
            'Teamized',
            'manage_options',
            'teamized-settings',
            'teamized_render_settings_page'
    );
}

add_action( 'admin_menu', 'teamized_add_settings_page' );

/**
 * Register settings
 */
function teamized_register_settings() {
    register_setting( 'teamized_settings_group', 'teamized_cache_duration', array(
            'type'              => 'integer',
            'sanitize_callback' => 'teamized_sanitize_cache_duration',
            'default'           => HOUR_IN_SECONDS,
    ) );

    add_settings_section(
            'teamized_cache_section',
            __( 'Cache Settings', 'wp-teamized' ),
            'teamized_cache_section_callback',
            'teamized-settings'
    );

    add_settings_field(
            'teamized_cache_duration',
            __( 'Cache Duration', 'wp-teamized' ),
            'teamized_cache_duration_field_callback',
            'teamized-settings',
            'teamized_cache_section'
    );
}

add_action( 'admin_init', 'teamized_register_settings' );

/**
 * Sanitize cache duration value
 */
function teamized_sanitize_cache_duration( $value ) {
    $value = absint( $value );
    // Ensure value is between 5 minutes and 24 hours
    if ( $value < 5 * MINUTE_IN_SECONDS ) {
        $value = 5 * MINUTE_IN_SECONDS;
    } elseif ( $value > 24 * HOUR_IN_SECONDS ) {
        $value = 24 * HOUR_IN_SECONDS;
    }

    return $value;
}

/**
 * Cache section description
 */
function teamized_cache_section_callback() {
    echo '<p>' . esc_html__( 'Configure how long API responses should be cached to improve performance.', 'wp-teamized' ) . '</p>';
}

/**
 * Cache duration field
 */
function teamized_cache_duration_field_callback() {
    $cache_duration         = get_option( 'teamized_cache_duration', HOUR_IN_SECONDS );

    ?>
    <select name="teamized_cache_duration" id="teamized_cache_duration">
        <option value="<?php echo esc_attr( 5 * MINUTE_IN_SECONDS ); ?>" <?php selected( $cache_duration, 5 * MINUTE_IN_SECONDS ); ?>>
            <?php esc_html_e( '5 minutes', 'wp-teamized' ); ?>
        </option>
        <option value="<?php echo esc_attr( 15 * MINUTE_IN_SECONDS ); ?>" <?php selected( $cache_duration, 15 * MINUTE_IN_SECONDS ); ?>>
            <?php esc_html_e( '15 minutes', 'wp-teamized' ); ?>
        </option>
        <option value="<?php echo esc_attr( 30 * MINUTE_IN_SECONDS ); ?>" <?php selected( $cache_duration, 30 * MINUTE_IN_SECONDS ); ?>>
            <?php esc_html_e( '30 minutes', 'wp-teamized' ); ?>
        </option>
        <option value="<?php echo esc_attr( HOUR_IN_SECONDS ); ?>" <?php selected( $cache_duration, HOUR_IN_SECONDS ); ?>>
            <?php esc_html_e( '1 hour', 'wp-teamized' ); ?>
        </option>
        <option value="<?php echo esc_attr( 2 * HOUR_IN_SECONDS ); ?>" <?php selected( $cache_duration, 2 * HOUR_IN_SECONDS ); ?>>
            <?php esc_html_e( '2 hours', 'wp-teamized' ); ?>
        </option>
        <option value="<?php echo esc_attr( 6 * HOUR_IN_SECONDS ); ?>" <?php selected( $cache_duration, 6 * HOUR_IN_SECONDS ); ?>>
            <?php esc_html_e( '6 hours', 'wp-teamized' ); ?>
        </option>
        <option value="<?php echo esc_attr( 12 * HOUR_IN_SECONDS ); ?>" <?php selected( $cache_duration, 12 * HOUR_IN_SECONDS ); ?>>
            <?php esc_html_e( '12 hours', 'wp-teamized' ); ?>
        </option>
        <option value="<?php echo esc_attr( 24 * HOUR_IN_SECONDS ); ?>" <?php selected( $cache_duration, 24 * HOUR_IN_SECONDS ); ?>>
            <?php esc_html_e( '24 hours', 'wp-teamized' ); ?>
        </option>
    </select>
    <p class="description">
        <?php esc_html_e( 'How long to store API responses in cache before fetching fresh data.', 'wp-teamized' ); ?>
    </p>
    <?php
}

/**
 * Handle cache clear action
 */
function teamized_handle_cache_clear() {
    if ( ! isset( $_POST['teamized_clear_cache_nonce'] ) || ! wp_verify_nonce( $_POST['teamized_clear_cache_nonce'], 'teamized_clear_cache' ) ) {
        return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Delete all teamized transients
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_teamized_api_%' OR option_name LIKE '_transient_timeout_teamized_api_%'" );

    add_settings_error(
            'teamized_messages',
            'teamized_cache_cleared',
            __( 'Cache cleared successfully.', 'wp-teamized' ),
            'success'
    );
}

add_action( 'admin_init', 'teamized_handle_cache_clear' );

/**
 * Render the settings page
 */
function teamized_render_settings_page() {
    // Check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Show settings errors/messages
    settings_errors( 'teamized_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <form action="options.php" method="post">
            <?php
            settings_fields( 'teamized_settings_group' );
            do_settings_sections( 'teamized-settings' );
            submit_button( __( 'Save Settings', 'wp-teamized' ) );
            ?>
        </form>

        <hr>

        <h2><?php esc_html_e( 'Cache Management', 'wp-teamized' ); ?></h2>
        <p><?php esc_html_e( 'Clear all cached API responses to force fresh data on the next request.', 'wp-teamized' ); ?></p>

        <form method="post" action="">
            <?php wp_nonce_field( 'teamized_clear_cache', 'teamized_clear_cache_nonce' ); ?>
            <?php submit_button( __( 'Clear Cache Now', 'wp-teamized' ), 'secondary', 'teamized_clear_cache', false ); ?>
        </form>

        <p>
            <?php esc_html_e( 'To force refresh the cache for a specific page without clearing all cache, add', 'wp-teamized' ); ?>
            <code>?teamized_refresh=1</code>
            <?php esc_html_e( 'to the page URL (administrators only).', 'wp-teamized' ); ?>
        </p>

    </div>
    <?php
}

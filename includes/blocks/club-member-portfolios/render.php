<?php
/**
 * Club Member Portfolios Block Render Callback
 *
 * @package Teamized
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetch data from Teamized API with caching
 *
 * @param string $api_url The API URL to fetch from.
 * @param bool $force_refresh Whether to bypass cache and force fresh data.
 *
 * @return array|WP_Error Array of data on success, WP_Error on failure.
 */
function teamized_fetch_api_data( $api_url, $force_refresh = false ) {
	// Validate API URL
	if ( empty( $api_url ) ) {
		return new WP_Error( 'missing_url', __( 'API URL is required.', 'wp-teamized' ) );
	}

	// Generate cache key based on URL
	$cache_key = 'teamized_api_' . md5( $api_url );

	// Try to get cached data unless force refresh is requested
	if ( ! $force_refresh ) {
		$data = get_transient( $cache_key );
		if ( false !== $data ) {
			return $data;
		}
	}

	// Fetch from API
	$response = wp_remote_get( $api_url );

	if ( is_wp_error( $response ) ) {
		// Cache error response for shorter duration to prevent repeated failed requests
		$error_cache_key = $cache_key . '_error';
		$error_cached    = get_transient( $error_cache_key );

		if ( false === $error_cached ) {
			set_transient( $error_cache_key, true, 5 * MINUTE_IN_SECONDS );
		}

		return new WP_Error( 'api_error', __( 'Error fetching data from API.', 'wp-teamized' ), $response );
	}

	// Parse response body
	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	// Validate data
	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_data', __( 'No data available or invalid response format.', 'wp-teamized' ) );
	}

	// Cache the successful response
	$cache_duration = get_option( 'teamized_cache_duration', HOUR_IN_SECONDS );
	set_transient( $cache_key, $data, $cache_duration );

	// Clear any error cache for this URL
	delete_transient( $cache_key . '_error' );

	return $data;
}

/**
 * Render the club member portfolios block on the frontend
 *
 * @param array $attributes Block attributes.
 *
 * @return string HTML output.
 */
function teamized_club_member_portfolios_render_block( $attributes ) {
	// Debug: verify function is called
	error_log( 'teamized_club_member_portfolios_render_block called with attributes: ' . print_r( $attributes, true ) );

	$api_url = isset( $attributes['apiUrl'] ) ? esc_url( $attributes['apiUrl'] ) : '';

	if ( empty( $api_url ) ) {
		return '<div class="teamized-portfolios"><p>' . esc_html__( 'Please configure the API URL in the block settings.', 'wp-teamized' ) . '</p></div>';
	}

	// Check if cache refresh is requested (admin only)
	$force_refresh = isset( $_GET['teamized_refresh'] ) && current_user_can( 'manage_options' );

	// Fetch data from API
	$data = teamized_fetch_api_data( $api_url, $force_refresh );

	// Handle errors
	if ( is_wp_error( $data ) ) {
		return '<div class="teamized-portfolios"><p>' . esc_html( $data->get_error_message() ) . '</p></div>';
	}

	// Render the portfolios
	$output = '<div class="teamized-portfolios">';

	// Render title (top-level name)
	if ( isset( $data['name'] ) && ! empty( $data['name'] ) ) {
		$output .= '<h2 class="teamized-title">' . esc_html( $data['name'] ) . '</h2>';
	}

	// Render subtitle (top-level description)
	if ( isset( $data['description'] ) && ! empty( $data['description'] ) ) {
		$output .= '<p class="teamized-description">' . esc_html( $data['description'] ) . '</p>';
	}

	// Render individual member portfolios
	if ( isset( $data['portfolios'] ) && is_array( $data['portfolios'] ) && ! empty( $data['portfolios'] ) ) {
		$output .= '<div class="teamized-members">';

		foreach ( $data['portfolios'] as $member ) {
			$output .= '<div class="teamized-member">';

			// Render member name
			$first_name = isset( $member['first_name'] ) ? $member['first_name'] : '';
			$last_name  = isset( $member['last_name'] ) ? $member['last_name'] : '';
			$full_name  = trim( $first_name . ' ' . $last_name );

			if ( ! empty( $full_name ) ) {
				$output .= '<h3 class="teamized-member-name">' . esc_html( $full_name ) . '</h3>';
			}

			$output .= '</div>';
		}

		$output .= '</div>';
	} else {
		$output .= '<p>' . esc_html__( 'No member portfolios found.', 'wp-teamized' ) . '</p>';
	}

	$output .= '</div>';

	return $output;
}

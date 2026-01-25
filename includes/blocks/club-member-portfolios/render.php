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
		return '<div class="teamized-portfolio-block"><p>' . esc_html__( 'Please configure the API URL in the block settings.', 'wp-teamized' ) . '</p></div>';
	}

	// Check if cache refresh is requested (admin only)
	$force_refresh = isset( $_GET['teamized_refresh'] ) && current_user_can( 'manage_options' );

	// Fetch data from API
	$data = teamized_fetch_api_data( $api_url, $force_refresh );

	// Handle errors
	if ( is_wp_error( $data ) ) {
		return '<div class="teamized-portfolio-block"><p>' . esc_html( $data->get_error_message() ) . '</p></div>';
	}

	// Render the portfolios
	$output = '<div class="teamized-portfolio-block">';

	// Render title (top-level name)
	if ( ! empty( $data['name'] ) ) {
		$output .= '<h2 class="teamized-title">' . esc_html( $data['name'] ) . '</h2>';
	}

	// Render subtitle (top-level description)
	if ( ! empty( $data['description'] ) ) {
		$output .= '<div class="teamized-description">' . nl2br( esc_html( $data['description'] ) ) . '</div>';
	}

	// Render individual member portfolios
	if ( is_array( $data['portfolios'] ) && ! empty( $data['portfolios'] ) ) {
		$output .= '<div class="teamized-members">';

		foreach ( $data['portfolios'] as $index => $member ) {
			// Get member data
			$first_name    = isset( $member['first_name'] ) ? $member['first_name'] : '';
			$last_name     = isset( $member['last_name'] ) ? $member['last_name'] : '';
			$full_name     = trim( $first_name . ' ' . $last_name );
			$role          = isset( $member['role'] ) ? $member['role'] : '';
			$image1_url    = ! empty( $member['image1_url'] ) ? $member['image1_url'] : 'https://placehold.co/400x400/e0e0e0/666?text=No+Image+1';
			$image2_url    = ! empty( $member['image2_url'] ) ? $member['image2_url'] : 'https://placehold.co/400x400/ffcc00/666?text=No+Image+2';
			$member_id     = isset( $member['id'] ) ? $member['id'] : 'member-' . $index;
			$member_since  = isset( $member['member_since'] ) ? $member['member_since'] : '';
			$hobby_since   = isset( $member['hobby_since'] ) ? $member['hobby_since'] : '';
			$profession    = isset( $member['profession'] ) ? $member['profession'] : '';
			$hobbies       = isset( $member['hobbies'] ) ? $member['hobbies'] : '';
			$highlights    = isset( $member['highlights'] ) ? $member['highlights'] : '';
			$biography     = isset( $member['biography'] ) ? $member['biography'] : '';
			$contact_email = isset( $member['contact_email'] ) ? $member['contact_email'] : '';

			// Render member card
			$output .= '<div class="teamized-member" role="button" tabindex="0" data-member-id="' . esc_attr( $member_id ) . '" aria-label="' . esc_attr( sprintf( __( 'View details for %s', 'wp-teamized' ), $full_name ) ) . '">';

			// Card image with hover effect
			$output .= '<div class="teamized-member-image">';
			$output .= '<div class="teamized-member-image-inner image1" style="background-image: url(' . esc_url( $image1_url ) . ');"></div>';
			if ( ! empty( $image2_url ) ) {
				$output .= '<div class="teamized-member-image-inner image2" style="background-image: url(' . esc_url( $image2_url ) . ');"></div>';
			}
			$output .= '</div>';

			// Card content
			$output .= '<div class="teamized-member-content">';

			if ( ! empty( $full_name ) ) {
				$output .= '<h3 class="teamized-member-name">' . esc_html( $full_name ) . '</h3>';
			}

			if ( ! empty( $role ) ) {
				$output .= '<p class="teamized-member-role">' . esc_html( $role ) . '</p>';
			}

			$output .= '</div>'; // .teamized-member-content
			$output .= '</div>'; // .teamized-member

			// Render modal for this member
			$output .= '<div class="teamized-modal-overlay" id="modal-' . esc_attr( $member_id ) . '" role="dialog" aria-modal="true" aria-labelledby="modal-title-' . esc_attr( $member_id ) . '">';
			$output .= '<div class="teamized-modal">';

			// Close button
			$output .= '<button class="teamized-modal-close" aria-label="' . esc_attr__( 'Close dialog', 'wp-teamized' ) . '">&times;</button>';

			// Modal header with images
			$output .= '<div class="teamized-modal-header">';
			$output .= '<div class="teamized-modal-image image1 active" style="background-image: url(' . esc_url( $image1_url ) . ');" data-image="image1"></div>';
			$output .= '<div class="teamized-modal-image image2" style="background-image: url(' . esc_url( $image2_url ) . ');" data-image="image2"></div>';
			$output .= '</div>';

			// Modal content
			$output .= '<div class="teamized-modal-content">';

			if ( ! empty( $full_name ) ) {
				$output .= '<h2 class="teamized-modal-name" id="modal-title-' . esc_attr( $member_id ) . '">' . esc_html( $full_name ) . '</h2>';
			}

			if ( ! empty( $role ) ) {
				$output .= '<p class="teamized-modal-role">' . esc_html( $role ) . '</p>';
			}

			// Modal fields
			$output .= '<div class="teamized-modal-fields">';

			// Member since
			if ( ! empty( $member_since ) ) {
				$output .= '<div class="teamized-field">';
				$output .= '<div class="teamized-field-label">' . esc_html__( 'Member since', 'wp-teamized' ) . '</div>';
				$output .= '<div class="teamized-field-value">' . esc_html( $member_since ) . '</div>';
				$output .= '</div>';
			}

			// Hobby since
			if ( ! empty( $hobby_since ) ) {
				$output .= '<div class="teamized-field">';
				$output .= '<div class="teamized-field-label">' . esc_html__( 'Hobby since', 'wp-teamized' ) . '</div>';
				$output .= '<div class="teamized-field-value">' . esc_html( $hobby_since ) . '</div>';
				$output .= '</div>';
			}

			// Profession
			if ( ! empty( $profession ) ) {
				$output .= '<div class="teamized-field">';
				$output .= '<div class="teamized-field-label">' . esc_html__( 'Profession', 'wp-teamized' ) . '</div>';
				$output .= '<div class="teamized-field-value">' . esc_html( $profession ) . '</div>';
				$output .= '</div>';
			}

			// Hobbies
			if ( ! empty( $hobbies ) ) {
				$output .= '<div class="teamized-field">';
				$output .= '<div class="teamized-field-label">' . esc_html__( 'Hobbies', 'wp-teamized' ) . '</div>';
				$output .= '<div class="teamized-field-value">' . esc_html( $hobbies ) . '</div>';
				$output .= '</div>';
			}

			// Highlights
			if ( ! empty( $highlights ) ) {
				$output .= '<div class="teamized-field">';
				$output .= '<div class="teamized-field-label">' . esc_html__( 'Highlights', 'wp-teamized' ) . '</div>';
				$output .= '<div class="teamized-field-value">' . esc_html( $highlights ) . '</div>';
				$output .= '</div>';
			}

			// Contact email
			if ( ! empty( $contact_email ) ) {
				$output .= '<div class="teamized-field">';
				$output .= '<div class="teamized-field-label">' . esc_html__( 'Contact', 'wp-teamized' ) . '</div>';
				$output .= '<div class="teamized-field-value"><a href="mailto:' . esc_attr( $contact_email ) . '">' . esc_html( $contact_email ) . '</a></div>';
				$output .= '</div>';
			}

			// Biography
			if ( ! empty( $biography ) ) {
				$output .= '<div class="teamized-field">';
				$output .= '<div class="teamized-field-label">' . esc_html__( 'Biography', 'wp-teamized' ) . '</div>';
				$output .= '<div class="teamized-field-value">' . esc_html( $biography ) . '</div>';
				$output .= '</div>';
			}

			$output .= '</div>'; // .teamized-modal-fields
			$output .= '</div>'; // .teamized-modal-content
			$output .= '</div>'; // .teamized-modal
			$output .= '</div>'; // .teamized-modal-overlay
		}

		$output .= '</div>'; // .teamized-members
	} else {
		$output .= '<p>' . esc_html__( 'No member portfolios found.', 'wp-teamized' ) . '</p>';
	}

	$output .= '</div>'; // .teamized-portfolio-block

	// Add inline JavaScript for modal functionality
	$output .= "
	<script>
	(function() {
		'use strict';
		
		// Open modal
		function openModal(modalId) {
			const modal = document.getElementById(modalId);
			if (!modal) return;
			
			modal.classList.add('active');
			document.body.style.overflow = 'hidden';
			
			// Focus the close button for accessibility
			modal.querySelector('.teamized-modal-close').focus();
			
			// Start image fade animation after 5s
			setTimeout(function() {
				modal.querySelector('.teamized-modal-image.image1').classList.remove('active');
				modal.querySelector('.teamized-modal-image.image2').classList.add('active');
			}, 5000);
		}
		
		// Close modal
		function closeModal(modal) {
			if (!modal) return;
			
			modal.classList.remove('active');
			document.body.style.overflow = '';
			
			// Reset images
			modal.querySelector('.teamized-modal-image.image1').classList.add('active');
			modal.querySelector('.teamized-modal-image.image2').classList.remove('active');
		}
		
		// Setup member cards
		const memberCards = document.querySelectorAll('.teamized-member');
		memberCards.forEach(function(card) {
			const memberId = card.getAttribute('data-member-id');
			const modalId = 'modal-' + memberId;
			
			// Click handler
			card.addEventListener('click', function() {
				openModal(modalId);
			});
			
			// Keyboard handler (Enter or Space)
			card.addEventListener('keydown', function(e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					openModal(modalId);
				}
			});
		});
		
		// Setup modal close buttons
		const closeButtons = document.querySelectorAll('.teamized-modal-close');
		closeButtons.forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				const modal = btn.closest('.teamized-modal-overlay');
				closeModal(modal);
			});
		});
		
		// Setup overlay click to close
		const overlays = document.querySelectorAll('.teamized-modal-overlay');
		overlays.forEach(function(overlay) {
			overlay.addEventListener('click', function(e) {
				if (e.target === overlay) {
					closeModal(overlay);
				}
			});
		});
		
		// Escape key to close modal
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				const activeModal = document.querySelector('.teamized-modal-overlay.active');
				if (activeModal) {
					closeModal(activeModal);
				}
			}
		});
	})();
	</script>
	";

	return $output;
}

<?php
/**
 * Block registration and rendering
 *
 * @package Teamized
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all Teamized blocks
 */
function teamized_register_blocks() {
	// Include block-specific render callbacks
	require_once __DIR__ . '/club-member-portfolios/render.php';

	// Register the block editor script with dependencies
	wp_register_script(
		'teamized-club-member-portfolios-editor',
		plugins_url( 'club-member-portfolios/index.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		filemtime( __DIR__ . '/club-member-portfolios/index.js' )
	);

	// Register the block stylesheet
	wp_register_style(
		'teamized-club-member-portfolios-style',
		plugins_url( 'club-member-portfolios/style.css', __FILE__ ),
		array(),
		filemtime( __DIR__ . '/club-member-portfolios/style.css' )
	);

	// Register club member portfolios block type from block.json
	register_block_type( __DIR__ . '/club-member-portfolios', array(
		'render_callback' => 'teamized_club_member_portfolios_render_block',
		'style'           => 'teamized-club-member-portfolios-style',
	) );
}

add_action( 'init', 'teamized_register_blocks' );


<?php
/**
 * Xebia Authority API Theme Functions
 *
 * Minimal theme for headless WordPress setup.
 */

declare(strict_types=1);

// Disable admin bar on frontend
add_filter('show_admin_bar', '__return_false');

// Remove unnecessary head elements
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

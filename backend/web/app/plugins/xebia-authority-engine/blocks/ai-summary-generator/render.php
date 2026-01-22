<?php

declare(strict_types=1);

/**
 * Server-side rendering for the AI Summary Generator block
 *
 * This file is loaded by block.json and provides the dynamic
 * rendering of the block on the frontend.
 *
 * @package Xebia\AuthorityEngine
 *
 * @var array $attributes Block attributes
 * @var string $content Block content
 * @var WP_Block $block Block instance
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
$summary = '';

// Get existing summary if available
if (function_exists('get_field')) {
    $summary = get_field('ai_executive_summary', $post_id) ?: '';
} else {
    $summary = get_post_meta($post_id, '_ai_executive_summary', true) ?: '';
}

// Context for WordPress Interactivity API
$context = [
    'postId' => $post_id,
    'summary' => $summary,
    'restUrl' => rest_url('xebia-authority/v1/'),
    'nonce' => wp_create_nonce('wp_rest'),
];

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'xebia-ai-summary-block',
    'data-wp-interactive' => 'xebia/ai-summary',
]);
?>

<div <?php echo $wrapper_attributes; ?>
    <?php echo wp_interactivity_data_wp_context($context); ?>>
    <div class="xebia-ai-summary-container">
        <div class="xebia-ai-summary-header">
            <h3 class="xebia-ai-summary-title">
                <svg class="xebia-ai-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                Executive Summary
            </h3>
            <span class="xebia-ai-badge">AI Generated</span>
        </div>

        <div class="xebia-ai-summary-content"
            data-wp-class--loading="state.isLoading"
            data-wp-class--has-error="state.error">

            <div data-wp-bind--hidden="!state.isLoading" class="xebia-ai-loading">
                <div class="xebia-ai-spinner"></div>
                <span>Generating executive summary...</span>
            </div>

            <div data-wp-bind--hidden="state.isLoading || state.error" class="xebia-ai-summary-text">
                <p data-wp-text="state.summary"><?php echo esc_html($summary); ?></p>
            </div>

            <div data-wp-bind--hidden="!state.error" class="xebia-ai-error">
                <span data-wp-text="state.error"></span>
            </div>
        </div>

        <div class="xebia-ai-summary-actions">
            <button
                class="xebia-ai-regenerate-btn"
                data-wp-on--click="actions.generateSummary"
                data-wp-bind--disabled="state.isLoading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M23 4v6h-6M1 20v-6h6"/>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                </svg>
                <span data-wp-text="state.isLoading ? 'Generating...' : 'Regenerate'">
                    <?php echo $summary ? 'Regenerate' : 'Generate'; ?>
                </span>
            </button>

            <button
                class="xebia-ai-trace-btn"
                data-wp-on--click="actions.toggleTraceability">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span>Traceability</span>
            </button>
        </div>

        <div class="xebia-ai-traceability" data-wp-bind--hidden="!state.showTraceability">
            <h4>AI Traceability Data</h4>
            <pre data-wp-text="state.traceabilityFormatted">No data available yet</pre>
        </div>
    </div>
</div>

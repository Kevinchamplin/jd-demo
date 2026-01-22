<?php

declare(strict_types=1);

namespace Xebia\AuthorityEngine\Blocks;

use Xebia\AuthorityEngine\OpenAI\SummaryService;

/**
 * AI Summary Generator Gutenberg Block
 *
 * Registers and handles the xebia/ai-summary-generator block
 * which provides AI-powered executive summary generation within
 * the WordPress block editor.
 *
 * @package Xebia\AuthorityEngine\Blocks
 */
final class AISummaryBlock
{
    private SummaryService $summaryService;

    /**
     * Block name
     */
    public const BLOCK_NAME = 'xebia/ai-summary-generator';

    public function __construct(SummaryService $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    /**
     * Register the block and its assets
     */
    public function register(): void
    {
        $this->registerBlockType();
        $this->registerEditorAssets();
        $this->registerInteractivityStore();
    }

    /**
     * Register the block type
     */
    private function registerBlockType(): void
    {
        register_block_type(
            XEBIA_AUTHORITY_ENGINE_PATH . 'blocks/ai-summary-generator',
            [
                'render_callback' => [$this, 'renderBlock'],
            ]
        );
    }

    /**
     * Register editor scripts and styles
     */
    private function registerEditorAssets(): void
    {
        $assetFile = XEBIA_AUTHORITY_ENGINE_PATH . 'blocks/ai-summary-generator/build/index.asset.php';
        $assets = file_exists($assetFile)
            ? require $assetFile
            : ['dependencies' => [], 'version' => XEBIA_AUTHORITY_ENGINE_VERSION];

        wp_register_script(
            'xebia-ai-summary-editor',
            XEBIA_AUTHORITY_ENGINE_URL . 'blocks/ai-summary-generator/build/index.js',
            $assets['dependencies'],
            $assets['version'],
            true
        );

        wp_register_style(
            'xebia-ai-summary-editor',
            XEBIA_AUTHORITY_ENGINE_URL . 'blocks/ai-summary-generator/build/index.css',
            [],
            $assets['version']
        );

        // Pass REST API configuration to editor
        wp_localize_script('xebia-ai-summary-editor', 'xebiaAISummary', [
            'restUrl' => rest_url('xebia-authority/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'postId' => get_the_ID(),
        ]);
    }

    /**
     * Register WordPress Interactivity API store
     */
    private function registerInteractivityStore(): void
    {
        if (!function_exists('wp_interactivity_state')) {
            return;
        }

        // Register the interactivity store for frontend
        add_action('wp_footer', function (): void {
            wp_interactivity_state('xebia/ai-summary', [
                'isLoading' => false,
                'summary' => '',
                'error' => '',
                'traceability' => null,
                'showTraceability' => false,
            ]);
        });
    }

    /**
     * Render the block on the frontend
     *
     * @param array<string, mixed> $attributes Block attributes
     * @param string               $content    Block content
     * @return string Rendered HTML
     */
    public function renderBlock(array $attributes, string $content): string
    {
        $postId = get_the_ID();
        $summary = '';

        // Get existing summary if available
        if (function_exists('get_field')) {
            $summary = get_field('ai_executive_summary', $postId) ?: '';
        } else {
            $summary = get_post_meta($postId, '_ai_executive_summary', true) ?: '';
        }

        $context = [
            'postId' => $postId,
            'summary' => $summary,
            'restUrl' => rest_url('xebia-authority/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
        ];

        ob_start();
        ?>
        <div
            <?php echo wp_kses_data(get_block_wrapper_attributes(['class' => 'xebia-ai-summary-block'])); ?>
            data-wp-interactive="xebia/ai-summary"
            <?php echo wp_kses_data(wp_interactivity_data_wp_context($context)); ?>
        >
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

                <div
                    class="xebia-ai-summary-content"
                    data-wp-class--loading="state.isLoading"
                    data-wp-class--has-error="state.error"
                >
                    <div data-wp-bind--hidden="!state.isLoading" class="xebia-ai-loading">
                        <div class="xebia-ai-spinner"></div>
                        <span>Generating executive summary...</span>
                    </div>

                    <div data-wp-bind--hidden="state.isLoading || state.error" class="xebia-ai-summary-text">
                        <p data-wp-text="state.summary"></p>
                    </div>

                    <div data-wp-bind--hidden="!state.error" class="xebia-ai-error">
                        <span data-wp-text="state.error"></span>
                    </div>
                </div>

                <div class="xebia-ai-summary-actions">
                    <button
                        class="xebia-ai-regenerate-btn"
                        data-wp-on--click="actions.generateSummary"
                        data-wp-bind--disabled="state.isLoading"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 4v6h-6M1 20v-6h6"/>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                        </svg>
                        <span data-wp-text="state.isLoading ? 'Generating...' : 'Regenerate'"></span>
                    </button>

                    <button
                        class="xebia-ai-trace-btn"
                        data-wp-on--click="actions.toggleTraceability"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <span>Traceability</span>
                    </button>
                </div>

                <div
                    class="xebia-ai-traceability"
                    data-wp-bind--hidden="!state.showTraceability"
                >
                    <h4>AI Traceability Data</h4>
                    <pre data-wp-text="state.traceabilityFormatted"></pre>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

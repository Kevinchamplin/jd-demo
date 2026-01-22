<?php

declare(strict_types=1);

namespace Xebia\AuthorityEngine\Services;

use RuntimeException;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use Xebia\AuthorityEngine\OpenAI\SummaryService;

/**
 * REST API Service
 *
 * Registers custom REST API endpoints for the AI summary generation
 * functionality. Used by the Gutenberg block and external integrations.
 *
 * @package Xebia\AuthorityEngine\Services
 */
final class RestApiService
{
    private SummaryService $summaryService;

    /**
     * REST API namespace
     */
    private const NAMESPACE = 'xebia-authority/v1';

    public function __construct(SummaryService $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    /**
     * Register REST API routes
     */
    public function register(): void
    {
        // Generate summary for a post
        register_rest_route(self::NAMESPACE, '/generate-summary/(?P<post_id>\d+)', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'generateSummaryForPost'],
            'permission_callback' => [$this, 'checkEditPermission'],
            'args' => [
                'post_id' => [
                    'required' => true,
                    'type' => 'integer',
                    'validate_callback' => static fn($value): bool => is_numeric($value) && $value > 0,
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);

        // Generate summary from content (preview)
        register_rest_route(self::NAMESPACE, '/preview-summary', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'previewSummary'],
            'permission_callback' => [$this, 'checkEditPermission'],
            'args' => [
                'content' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'wp_kses_post',
                ],
                'title' => [
                    'required' => false,
                    'type' => 'string',
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);

        // Get traceability data
        register_rest_route(self::NAMESPACE, '/traceability', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'getTraceability'],
            'permission_callback' => [$this, 'checkEditPermission'],
        ]);

        // Health check endpoint
        register_rest_route(self::NAMESPACE, '/health', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'healthCheck'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Generate summary for an existing post
     */
    public function generateSummaryForPost(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $postId = $request->get_param('post_id');

        // Verify nonce from header
        $nonce = $request->get_header('X-WP-Nonce');
        if (!$nonce || !wp_verify_nonce($nonce, 'wp_rest')) {
            return new WP_Error(
                'rest_invalid_nonce',
                'Invalid or missing nonce.',
                ['status' => 403]
            );
        }

        try {
            $result = $this->summaryService->generateAndSaveForPost($postId);
            $traceData = $this->summaryService->getTraceabilityData();

            return new WP_REST_Response([
                'success' => true,
                'data' => [
                    'summary' => $result['summary'],
                    'metadata' => $result['metadata'],
                    'traceability' => $traceData,
                ],
            ], 200);
        } catch (RuntimeException $e) {
            $traceData = $this->summaryService->getTraceabilityData();

            return new WP_REST_Response([
                'success' => false,
                'error' => $e->getMessage(),
                'traceability' => $traceData,
            ], 500);
        }
    }

    /**
     * Preview summary without saving
     */
    public function previewSummary(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $content = $request->get_param('content');
        $title = $request->get_param('title');

        // Verify nonce from header
        $nonce = $request->get_header('X-WP-Nonce');
        if (!$nonce || !wp_verify_nonce($nonce, 'wp_rest')) {
            return new WP_Error(
                'rest_invalid_nonce',
                'Invalid or missing nonce.',
                ['status' => 403]
            );
        }

        if (empty(trim($content))) {
            return new WP_Error(
                'rest_invalid_content',
                'Content cannot be empty.',
                ['status' => 400]
            );
        }

        try {
            $result = $this->summaryService->generateExecutiveSummary($content, $title);
            $traceData = $this->summaryService->getTraceabilityData();

            return new WP_REST_Response([
                'success' => true,
                'data' => [
                    'summary' => $result['summary'],
                    'metadata' => $result['metadata'],
                    'traceability' => $traceData,
                ],
            ], 200);
        } catch (RuntimeException $e) {
            $traceData = $this->summaryService->getTraceabilityData();

            return new WP_REST_Response([
                'success' => false,
                'error' => $e->getMessage(),
                'traceability' => $traceData,
            ], 500);
        }
    }

    /**
     * Get traceability data from the last API call
     */
    public function getTraceability(WP_REST_Request $request): WP_REST_Response
    {
        $traceData = $this->summaryService->getTraceabilityData();

        return new WP_REST_Response([
            'success' => true,
            'data' => $traceData,
        ], 200);
    }

    /**
     * Health check endpoint
     */
    public function healthCheck(WP_REST_Request $request): WP_REST_Response
    {
        return new WP_REST_Response([
            'status' => 'healthy',
            'plugin' => 'xebia-authority-engine',
            'version' => XEBIA_AUTHORITY_ENGINE_VERSION,
            'timestamp' => gmdate('c'),
        ], 200);
    }

    /**
     * Permission callback - check if user can edit posts
     */
    public function checkEditPermission(): bool
    {
        return current_user_can('edit_posts');
    }
}

<?php

declare(strict_types=1);

namespace Xebia\AuthorityEngine\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;
use RuntimeException;
use WPGraphQL\AppContext;
use Xebia\AuthorityEngine\OpenAI\SummaryService;

/**
 * GraphQL Type and Field Provider
 *
 * Registers custom GraphQL types, fields, and mutations for the
 * Xebia Authority Engine plugin with WPGraphQL.
 *
 * @package Xebia\AuthorityEngine\GraphQL
 */
final class GraphQLProvider
{
    private SummaryService $summaryService;

    public function __construct(SummaryService $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    /**
     * Register all GraphQL types, fields, and mutations
     */
    public function register(): void
    {
        $this->registerTypes();
        $this->registerFields();
        $this->registerMutations();
    }

    /**
     * Register custom GraphQL types
     */
    private function registerTypes(): void
    {
        // AI Summary Metadata Type
        register_graphql_object_type('AISummaryMetadata', [
            'description' => 'Metadata about an AI-generated summary',
            'fields' => [
                'model' => [
                    'type' => 'String',
                    'description' => 'The AI model used for generation',
                ],
                'generatedAt' => [
                    'type' => 'String',
                    'description' => 'ISO timestamp of when the summary was generated',
                ],
                'contentLength' => [
                    'type' => 'Int',
                    'description' => 'Length of the original content in characters',
                ],
                'summaryLength' => [
                    'type' => 'Int',
                    'description' => 'Length of the generated summary in characters',
                ],
                'tokensUsed' => [
                    'type' => 'Int',
                    'description' => 'Total tokens consumed in the API call',
                ],
                'finishReason' => [
                    'type' => 'String',
                    'description' => 'Reason the AI stopped generating',
                ],
            ],
        ]);

        // AI Summary Response Type
        register_graphql_object_type('AISummaryResponse', [
            'description' => 'Response containing an AI-generated executive summary',
            'fields' => [
                'summary' => [
                    'type' => 'String',
                    'description' => 'The generated executive summary',
                ],
                'metadata' => [
                    'type' => 'AISummaryMetadata',
                    'description' => 'Metadata about the generation process',
                ],
                'success' => [
                    'type' => 'Boolean',
                    'description' => 'Whether the generation was successful',
                ],
                'errorMessage' => [
                    'type' => 'String',
                    'description' => 'Error message if generation failed',
                ],
            ],
        ]);

        // AI Traceability Type
        register_graphql_object_type('AITraceability', [
            'description' => 'Full traceability data for AI debugging',
            'fields' => [
                'requestPayload' => [
                    'type' => 'String',
                    'description' => 'JSON string of the request sent to OpenAI',
                ],
                'responseData' => [
                    'type' => 'String',
                    'description' => 'JSON string of the response from OpenAI',
                ],
                'durationMs' => [
                    'type' => 'Float',
                    'description' => 'Request duration in milliseconds',
                ],
                'timestamp' => [
                    'type' => 'String',
                    'description' => 'ISO timestamp of the request',
                ],
            ],
        ]);
    }

    /**
     * Register custom GraphQL fields on existing types
     */
    private function registerFields(): void
    {
        // Add aiSummary field to Insight post type
        register_graphql_field('Insight', 'aiSummary', [
            'type' => 'String',
            'description' => 'AI-generated executive summary of the insight',
            'resolve' => function ($post): ?string {
                if (function_exists('get_field')) {
                    return get_field('ai_executive_summary', $post->ID) ?: null;
                }
                return get_post_meta($post->ID, '_ai_executive_summary', true) ?: null;
            },
        ]);

        // Add aiSummaryMeta field to Insight post type
        register_graphql_field('Insight', 'aiSummaryMeta', [
            'type' => 'AISummaryMetadata',
            'description' => 'Metadata about the AI-generated summary',
            'resolve' => function ($post): ?array {
                $metadata = null;

                if (function_exists('get_field')) {
                    $metadata = get_field('ai_summary_metadata', $post->ID);
                } else {
                    $metadata = get_post_meta($post->ID, '_ai_summary_metadata', true);
                }

                if (is_string($metadata)) {
                    $metadata = json_decode($metadata, true);
                }

                if (!is_array($metadata)) {
                    return null;
                }

                return [
                    'model' => $metadata['model'] ?? null,
                    'generatedAt' => $metadata['generated_at'] ?? null,
                    'contentLength' => $metadata['content_length'] ?? null,
                    'summaryLength' => $metadata['summary_length'] ?? null,
                    'tokensUsed' => $metadata['usage']['total_tokens'] ?? null,
                    'finishReason' => $metadata['finish_reason'] ?? null,
                ];
            },
        ]);

        // Add hasAISummary convenience field
        register_graphql_field('Insight', 'hasAISummary', [
            'type' => 'Boolean',
            'description' => 'Whether this insight has an AI-generated summary',
            'resolve' => function ($post): bool {
                if (function_exists('get_field')) {
                    return !empty(get_field('ai_executive_summary', $post->ID));
                }
                return !empty(get_post_meta($post->ID, '_ai_executive_summary', true));
            },
        ]);
    }

    /**
     * Register GraphQL mutations
     */
    private function registerMutations(): void
    {
        // Generate AI Summary mutation
        register_graphql_mutation('generateAISummary', [
            'description' => 'Generate an AI executive summary for an insight post',
            'inputFields' => [
                'postId' => [
                    'type' => ['non_null' => 'Int'],
                    'description' => 'The ID of the insight post to summarize',
                ],
            ],
            'outputFields' => [
                'summary' => [
                    'type' => 'String',
                    'description' => 'The generated executive summary',
                ],
                'metadata' => [
                    'type' => 'AISummaryMetadata',
                    'description' => 'Metadata about the generation',
                ],
                'success' => [
                    'type' => 'Boolean',
                    'description' => 'Whether generation succeeded',
                ],
                'errorMessage' => [
                    'type' => 'String',
                    'description' => 'Error message if failed',
                ],
                'traceability' => [
                    'type' => 'AITraceability',
                    'description' => 'Full request/response traceability data',
                ],
            ],
            'mutateAndGetPayload' => function (array $input, AppContext $context, ResolveInfo $info): array {
                // Verify user has permission
                if (!current_user_can('edit_posts')) {
                    return [
                        'success' => false,
                        'errorMessage' => 'You do not have permission to generate summaries',
                        'summary' => null,
                        'metadata' => null,
                        'traceability' => null,
                    ];
                }

                $postId = (int) $input['postId'];

                try {
                    $result = $this->summaryService->generateAndSaveForPost($postId);
                    $traceData = $this->summaryService->getTraceabilityData();

                    return [
                        'success' => true,
                        'summary' => $result['summary'],
                        'metadata' => [
                            'model' => $result['metadata']['model'] ?? null,
                            'generatedAt' => $result['metadata']['generated_at'] ?? null,
                            'contentLength' => $result['metadata']['content_length'] ?? null,
                            'summaryLength' => $result['metadata']['summary_length'] ?? null,
                            'tokensUsed' => $result['metadata']['usage']['total_tokens'] ?? null,
                            'finishReason' => $result['metadata']['finish_reason'] ?? null,
                        ],
                        'traceability' => [
                            'requestPayload' => wp_json_encode($traceData['request']),
                            'responseData' => wp_json_encode($traceData['response']),
                            'durationMs' => $traceData['duration_ms'],
                            'timestamp' => $traceData['timestamp'],
                        ],
                        'errorMessage' => null,
                    ];
                } catch (RuntimeException $e) {
                    $traceData = $this->summaryService->getTraceabilityData();

                    return [
                        'success' => false,
                        'errorMessage' => $e->getMessage(),
                        'summary' => null,
                        'metadata' => null,
                        'traceability' => [
                            'requestPayload' => wp_json_encode($traceData['request']),
                            'responseData' => wp_json_encode($traceData['response']),
                            'durationMs' => $traceData['duration_ms'],
                            'timestamp' => $traceData['timestamp'],
                        ],
                    ];
                }
            },
        ]);

        // Generate summary from arbitrary content (for preview)
        register_graphql_mutation('previewAISummary', [
            'description' => 'Preview an AI summary without saving it',
            'inputFields' => [
                'content' => [
                    'type' => ['non_null' => 'String'],
                    'description' => 'The content to summarize',
                ],
                'title' => [
                    'type' => 'String',
                    'description' => 'Optional title for context',
                ],
            ],
            'outputFields' => [
                'summary' => [
                    'type' => 'String',
                    'description' => 'The generated executive summary',
                ],
                'success' => [
                    'type' => 'Boolean',
                    'description' => 'Whether generation succeeded',
                ],
                'errorMessage' => [
                    'type' => 'String',
                    'description' => 'Error message if failed',
                ],
                'traceability' => [
                    'type' => 'AITraceability',
                    'description' => 'Full request/response traceability data',
                ],
            ],
            'mutateAndGetPayload' => function (array $input): array {
                if (!current_user_can('edit_posts')) {
                    return [
                        'success' => false,
                        'errorMessage' => 'You do not have permission to generate summaries',
                        'summary' => null,
                        'traceability' => null,
                    ];
                }

                try {
                    $result = $this->summaryService->generateExecutiveSummary(
                        $input['content'],
                        $input['title'] ?? ''
                    );
                    $traceData = $this->summaryService->getTraceabilityData();

                    return [
                        'success' => true,
                        'summary' => $result['summary'],
                        'errorMessage' => null,
                        'traceability' => [
                            'requestPayload' => wp_json_encode($traceData['request']),
                            'responseData' => wp_json_encode($traceData['response']),
                            'durationMs' => $traceData['duration_ms'],
                            'timestamp' => $traceData['timestamp'],
                        ],
                    ];
                } catch (RuntimeException $e) {
                    $traceData = $this->summaryService->getTraceabilityData();

                    return [
                        'success' => false,
                        'errorMessage' => $e->getMessage(),
                        'summary' => null,
                        'traceability' => [
                            'requestPayload' => wp_json_encode($traceData['request']),
                            'responseData' => wp_json_encode($traceData['response']),
                            'durationMs' => $traceData['duration_ms'],
                            'timestamp' => $traceData['timestamp'],
                        ],
                    ];
                }
            },
        ]);
    }
}

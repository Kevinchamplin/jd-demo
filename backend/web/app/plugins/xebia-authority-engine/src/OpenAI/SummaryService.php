<?php

declare(strict_types=1);

namespace Xebia\AuthorityEngine\OpenAI;

use RuntimeException;

/**
 * AI Summary Service
 *
 * Generates executive summaries of content using OpenAI's GPT models.
 * Provides structured output with full traceability.
 *
 * @package Xebia\AuthorityEngine\OpenAI
 */
final class SummaryService
{
    private OpenAIClient $client;

    /**
     * System prompt for executive summary generation
     */
    private const SYSTEM_PROMPT = <<<'PROMPT'
You are an expert content analyst for Xebia, a leading technology consultancy known for "Quality without Compromise" and "Sharing Knowledge."

Your task is to create executive summaries that:
1. Capture the core value proposition in 2-3 sentences
2. Highlight key technical insights or innovations
3. Identify actionable takeaways for technical leaders
4. Maintain Xebia's tone: authoritative yet accessible, technically precise yet engaging

Format your response as a concise executive summary suitable for C-level stakeholders and technical decision-makers.
PROMPT;

    public function __construct(OpenAIClient $client)
    {
        $this->client = $client;
    }

    /**
     * Generate an executive summary for the given content
     *
     * @param string $content The content to summarize
     * @param string $title   Optional title for context
     * @return array{summary: string, metadata: array<string, mixed>}
     * @throws RuntimeException On generation failure
     */
    public function generateExecutiveSummary(string $content, string $title = ''): array
    {
        $content = $this->sanitizeContent($content);

        if (empty($content)) {
            throw new RuntimeException('Cannot generate summary for empty content');
        }

        $userPrompt = $this->buildUserPrompt($content, $title);

        $result = $this->client->chatCompletion([
            ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
            ['role' => 'user', 'content' => $userPrompt],
        ]);

        return [
            'summary' => trim($result['content']),
            'metadata' => [
                'model' => $result['model'],
                'usage' => $result['usage'],
                'finish_reason' => $result['finish_reason'],
                'generated_at' => gmdate('c'),
                'content_length' => strlen($content),
                'summary_length' => strlen($result['content']),
            ],
        ];
    }

    /**
     * Generate summary and save to post meta
     *
     * @param int $postId The post ID to generate summary for
     * @return array{summary: string, metadata: array<string, mixed>}
     * @throws RuntimeException On failure
     */
    public function generateAndSaveForPost(int $postId): array
    {
        $post = get_post($postId);

        if (!$post) {
            throw new RuntimeException(sprintf('Post with ID %d not found', $postId));
        }

        // Get plain text content from post
        $content = wp_strip_all_tags($post->post_content);
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $result = $this->generateExecutiveSummary($content, $post->post_title);

        // Save to ACF field if available
        if (function_exists('update_field')) {
            update_field('ai_executive_summary', $result['summary'], $postId);
            update_field('ai_summary_metadata', wp_json_encode($result['metadata']), $postId);
        } else {
            // Fallback to post meta
            update_post_meta($postId, '_ai_executive_summary', $result['summary']);
            update_post_meta($postId, '_ai_summary_metadata', $result['metadata']);
        }

        return $result;
    }

    /**
     * Get traceability data for the last API call
     *
     * @return array<string, mixed>
     */
    public function getTraceabilityData(): array
    {
        return $this->client->getTraceabilityData();
    }

    /**
     * Build the user prompt for summary generation
     */
    private function buildUserPrompt(string $content, string $title): string
    {
        $prompt = 'Please create an executive summary for the following content';

        if (!empty($title)) {
            $prompt .= sprintf(' titled "%s"', $title);
        }

        $prompt .= ":\n\n---\n\n" . $content . "\n\n---";

        return $prompt;
    }

    /**
     * Sanitize content for API submission
     */
    private function sanitizeContent(string $content): string
    {
        // Strip HTML tags
        $content = wp_strip_all_tags($content);

        // Decode HTML entities
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize whitespace
        $content = preg_replace('/\s+/', ' ', $content) ?? $content;

        // Trim and limit length (GPT-4 has large context, but we limit for cost)
        $content = trim($content);

        // Limit to approximately 4000 words (roughly 16000 characters)
        if (strlen($content) > 16000) {
            $content = substr($content, 0, 16000);
            // Try to cut at a sentence boundary
            $lastPeriod = strrpos($content, '.');
            if ($lastPeriod !== false && $lastPeriod > 14000) {
                $content = substr($content, 0, $lastPeriod + 1);
            }
        }

        return $content;
    }
}

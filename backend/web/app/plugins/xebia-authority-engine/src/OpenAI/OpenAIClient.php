<?php

declare(strict_types=1);

namespace Xebia\AuthorityEngine\OpenAI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use RuntimeException;
use Xebia\AuthorityEngine\Services\ConfigService;

/**
 * OpenAI API Client
 *
 * Handles HTTP communication with the OpenAI API using Guzzle.
 * Implements proper error handling and response parsing.
 *
 * @package Xebia\AuthorityEngine\OpenAI
 */
final class OpenAIClient
{
    private Client $httpClient;

    private ConfigService $config;

    /**
     * Last request payload for debugging/traceability
     *
     * @var array<string, mixed>|null
     */
    private ?array $lastRequestPayload = null;

    /**
     * Last response data for debugging/traceability
     *
     * @var array<string, mixed>|null
     */
    private ?array $lastResponseData = null;

    /**
     * Last request duration in milliseconds
     */
    private ?float $lastRequestDuration = null;

    public function __construct(ConfigService $config)
    {
        $this->config = $config;
        $this->httpClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'timeout' => 30.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config->getOpenAIApiKey(),
            ],
        ]);
    }

    /**
     * Send a chat completion request to OpenAI
     *
     * @param array<array{role: string, content: string}> $messages
     * @param array<string, mixed> $options Additional options
     * @return array{content: string, usage: array<string, int>, model: string, finish_reason: string}
     * @throws RuntimeException On API errors
     */
    public function chatCompletion(array $messages, array $options = []): array
    {
        $payload = [
            'model' => $options['model'] ?? $this->config->getOpenAIModel(),
            'messages' => $messages,
            'max_tokens' => $options['max_tokens'] ?? $this->config->getOpenAIMaxTokens(),
            'temperature' => $options['temperature'] ?? 0.7,
        ];

        $this->lastRequestPayload = $payload;
        $startTime = microtime(true);

        try {
            $response = $this->httpClient->post('chat/completions', [
                RequestOptions::JSON => $payload,
            ]);

            $this->lastRequestDuration = (microtime(true) - $startTime) * 1000;

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            $this->lastResponseData = $data;

            if (!isset($data['choices'][0]['message']['content'])) {
                throw new RuntimeException('Invalid response structure from OpenAI API');
            }

            return [
                'content' => $data['choices'][0]['message']['content'],
                'usage' => $data['usage'] ?? [],
                'model' => $data['model'] ?? $payload['model'],
                'finish_reason' => $data['choices'][0]['finish_reason'] ?? 'unknown',
            ];
        } catch (GuzzleException $e) {
            $this->lastRequestDuration = (microtime(true) - $startTime) * 1000;
            $this->lastResponseData = ['error' => $e->getMessage()];

            throw new RuntimeException(
                sprintf('OpenAI API request failed: %s', $e->getMessage()),
                (int) $e->getCode(),
                $e
            );
        } catch (\JsonException $e) {
            throw new RuntimeException(
                sprintf('Failed to parse OpenAI response: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }

    /**
     * Get the last request payload for debugging
     *
     * @return array<string, mixed>|null
     */
    public function getLastRequestPayload(): ?array
    {
        return $this->lastRequestPayload;
    }

    /**
     * Get the last response data for debugging
     *
     * @return array<string, mixed>|null
     */
    public function getLastResponseData(): ?array
    {
        return $this->lastResponseData;
    }

    /**
     * Get the last request duration in milliseconds
     */
    public function getLastRequestDuration(): ?float
    {
        return $this->lastRequestDuration;
    }

    /**
     * Get full traceability data for the last request
     *
     * @return array<string, mixed>
     */
    public function getTraceabilityData(): array
    {
        return [
            'request' => $this->sanitizeForLogging($this->lastRequestPayload),
            'response' => $this->sanitizeForLogging($this->lastResponseData),
            'duration_ms' => $this->lastRequestDuration,
            'timestamp' => gmdate('c'),
            'model' => $this->config->getOpenAIModel(),
        ];
    }

    /**
     * Sanitize data for logging (remove sensitive information)
     *
     * @param array<string, mixed>|null $data
     * @return array<string, mixed>|null
     */
    private function sanitizeForLogging(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        // Create a copy to avoid modifying original
        $sanitized = $data;

        // Remove any potential API keys that might be in the data
        if (isset($sanitized['api_key'])) {
            $sanitized['api_key'] = '***REDACTED***';
        }

        return $sanitized;
    }

    /**
     * Test the API connection
     *
     * @return array{success: bool, message: string, model: string|null}
     */
    public function testConnection(): array
    {
        try {
            $result = $this->chatCompletion([
                ['role' => 'user', 'content' => 'Say "connection successful" in exactly two words.'],
            ], ['max_tokens' => 10]);

            return [
                'success' => true,
                'message' => 'Connection successful',
                'model' => $result['model'],
            ];
        } catch (RuntimeException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'model' => null,
            ];
        }
    }
}

<?php

declare(strict_types=1);

namespace Xebia\AuthorityEngine\Services;

use RuntimeException;

/**
 * Configuration Service
 *
 * Centralized configuration management for the plugin.
 * Retrieves values from WordPress constants and environment variables.
 *
 * @package Xebia\AuthorityEngine\Services
 */
final class ConfigService
{
    /**
     * Configuration cache
     *
     * @var array<string, mixed>
     */
    private array $cache = [];

    /**
     * Get OpenAI API key
     *
     * @throws RuntimeException If API key is not configured
     */
    public function getOpenAIApiKey(): string
    {
        $key = $this->get('OPENAI_API_KEY');

        if (empty($key)) {
            throw new RuntimeException(
                'OpenAI API key is not configured. Please set OPENAI_API_KEY in your .env file.'
            );
        }

        return $key;
    }

    /**
     * Get OpenAI model identifier
     */
    public function getOpenAIModel(): string
    {
        return $this->get('OPENAI_MODEL', 'gpt-4o');
    }

    /**
     * Get maximum tokens for OpenAI responses
     */
    public function getOpenAIMaxTokens(): int
    {
        return (int) $this->get('OPENAI_MAX_TOKENS', 500);
    }

    /**
     * Get the OpenAI API endpoint
     */
    public function getOpenAIEndpoint(): string
    {
        return $this->get('OPENAI_ENDPOINT', 'https://api.openai.com/v1/chat/completions');
    }

    /**
     * Check if we're in debug mode
     */
    public function isDebugMode(): bool
    {
        return defined('WP_DEBUG') && WP_DEBUG;
    }

    /**
     * Check if GraphQL debugging is enabled
     */
    public function isGraphQLDebugEnabled(): bool
    {
        return defined('GRAPHQL_DEBUG') && GRAPHQL_DEBUG;
    }

    /**
     * Get plugin version
     */
    public function getPluginVersion(): string
    {
        return XEBIA_AUTHORITY_ENGINE_VERSION;
    }

    /**
     * Get plugin path
     */
    public function getPluginPath(): string
    {
        return XEBIA_AUTHORITY_ENGINE_PATH;
    }

    /**
     * Get plugin URL
     */
    public function getPluginUrl(): string
    {
        return XEBIA_AUTHORITY_ENGINE_URL;
    }

    /**
     * Get a configuration value
     *
     * @param string $key     Configuration key (constant name)
     * @param mixed  $default Default value if not found
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $value = $default;

        // Check WordPress constant first
        if (defined($key)) {
            $value = constant($key);
        }

        // Check environment variable as fallback
        $envValue = getenv($key);
        if ($envValue !== false) {
            $value = $envValue;
        }

        $this->cache[$key] = $value;

        return $value;
    }

    /**
     * Get all OpenAI configuration as array
     *
     * @return array<string, mixed>
     */
    public function getOpenAIConfig(): array
    {
        return [
            'api_key' => $this->hasOpenAIApiKey() ? '***configured***' : 'NOT_SET',
            'model' => $this->getOpenAIModel(),
            'max_tokens' => $this->getOpenAIMaxTokens(),
            'endpoint' => $this->getOpenAIEndpoint(),
        ];
    }

    /**
     * Check if OpenAI API key is configured
     */
    public function hasOpenAIApiKey(): bool
    {
        try {
            $key = $this->getOpenAIApiKey();
            return !empty($key) && $key !== 'your-openai-api-key';
        } catch (RuntimeException) {
            return false;
        }
    }

    /**
     * Clear configuration cache
     */
    public function clearCache(): void
    {
        $this->cache = [];
    }
}

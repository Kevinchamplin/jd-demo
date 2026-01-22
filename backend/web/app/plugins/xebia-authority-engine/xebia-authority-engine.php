<?php

declare(strict_types=1);

/**
 * Plugin Name: Xebia Authority Engine
 * Plugin URI: https://xebia.com
 * Description: AI-powered content engine for the Xebia Authority Hub. Demonstrates modern PHP architecture with DI container, PSR-12 standards, and AI integration.
 * Version: 1.0.0
 * Author: Kevin Champlin
 * Author URI: https://xebia.com
 * License: MIT
 * Requires PHP: 8.2
 * Requires at least: 6.4
 *
 * @package Xebia\AuthorityEngine
 */

namespace Xebia\AuthorityEngine;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('XEBIA_AUTHORITY_ENGINE_VERSION', '1.0.0');
define('XEBIA_AUTHORITY_ENGINE_PATH', plugin_dir_path(__FILE__));
define('XEBIA_AUTHORITY_ENGINE_URL', plugin_dir_url(__FILE__));
define('XEBIA_AUTHORITY_ENGINE_BASENAME', plugin_basename(__FILE__));

/**
 * Autoloader for plugin classes
 *
 * Uses PSR-4 autoloading standard
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'Xebia\\AuthorityEngine\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Initialize the plugin
 *
 * Uses a static method to ensure single initialization point
 */
final class Plugin
{
    private static ?self $instance = null;

    private Container\Container $container;

    private function __construct()
    {
        $this->container = new Container\Container();
        $this->registerServices();
        $this->bootServices();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getContainer(): Container\Container
    {
        return $this->container;
    }

    private function registerServices(): void
    {
        // Register core services
        $this->container->singleton(
            Services\ConfigService::class,
            static fn(): Services\ConfigService => new Services\ConfigService()
        );

        $this->container->singleton(
            OpenAI\OpenAIClient::class,
            fn(): OpenAI\OpenAIClient => new OpenAI\OpenAIClient(
                $this->container->get(Services\ConfigService::class)
            )
        );

        $this->container->singleton(
            OpenAI\SummaryService::class,
            fn(): OpenAI\SummaryService => new OpenAI\SummaryService(
                $this->container->get(OpenAI\OpenAIClient::class)
            )
        );

        $this->container->singleton(
            PostTypes\InsightPostType::class,
            static fn(): PostTypes\InsightPostType => new PostTypes\InsightPostType()
        );

        $this->container->singleton(
            GraphQL\GraphQLProvider::class,
            fn(): GraphQL\GraphQLProvider => new GraphQL\GraphQLProvider(
                $this->container->get(OpenAI\SummaryService::class)
            )
        );

        $this->container->singleton(
            Blocks\AISummaryBlock::class,
            fn(): Blocks\AISummaryBlock => new Blocks\AISummaryBlock(
                $this->container->get(OpenAI\SummaryService::class)
            )
        );

        $this->container->singleton(
            Services\RestApiService::class,
            fn(): Services\RestApiService => new Services\RestApiService(
                $this->container->get(OpenAI\SummaryService::class)
            )
        );
    }

    private function bootServices(): void
    {
        // Boot all registered services
        add_action('init', function (): void {
            $this->container->get(PostTypes\InsightPostType::class)->register();
        });

        add_action('graphql_register_types', function (): void {
            $this->container->get(GraphQL\GraphQLProvider::class)->register();
        });

        add_action('init', function (): void {
            $this->container->get(Blocks\AISummaryBlock::class)->register();
        });

        add_action('rest_api_init', function (): void {
            $this->container->get(Services\RestApiService::class)->register();
        });

        // Register ACF fields if ACF is active
        add_action('acf/init', function (): void {
            $this->registerAcfFields();
        });
    }

    private function registerAcfFields(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_xebia_insight_ai',
            'title' => 'AI Generated Content',
            'fields' => [
                [
                    'key' => 'field_ai_executive_summary',
                    'label' => 'AI Executive Summary',
                    'name' => 'ai_executive_summary',
                    'type' => 'textarea',
                    'instructions' => 'AI-generated executive summary of the content.',
                    'required' => 0,
                    'readonly' => 1,
                    'rows' => 4,
                ],
                [
                    'key' => 'field_ai_summary_metadata',
                    'label' => 'AI Summary Metadata',
                    'name' => 'ai_summary_metadata',
                    'type' => 'textarea',
                    'instructions' => 'JSON metadata from the AI generation process.',
                    'required' => 0,
                    'readonly' => 1,
                    'rows' => 6,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'xebia_insight',
                    ],
                ],
            ],
            'show_in_graphql' => true,
            'graphql_field_name' => 'aiContent',
        ]);
    }
}

// Initialize plugin on plugins_loaded hook
add_action('plugins_loaded', static function (): void {
    Plugin::getInstance();
}, 10);

// Activation hook
register_activation_hook(__FILE__, static function (): void {
    // Flush rewrite rules on activation
    add_action('init', static function (): void {
        flush_rewrite_rules();
    }, 99);
});

// Deactivation hook
register_deactivation_hook(__FILE__, static function (): void {
    flush_rewrite_rules();
});

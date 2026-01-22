<?php

declare(strict_types=1);

/**
 * Development Environment Configuration
 *
 * @package Xebia\AuthorityHub
 */

use Roots\WPConfig\Config;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', true);
Config::define('WP_DEBUG_LOG', '/tmp/wp-errors.log');
Config::define('SCRIPT_DEBUG', true);
Config::define('DISALLOW_INDEXING', true);

ini_set('display_errors', '1');

// Enable WPGraphQL debugging
Config::define('GRAPHQL_DEBUG', true);

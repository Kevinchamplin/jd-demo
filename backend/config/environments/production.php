<?php

declare(strict_types=1);

/**
 * Production Environment Configuration
 *
 * @package Xebia\AuthorityHub
 */

use Roots\WPConfig\Config;

Config::define('WP_DEBUG', false);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', false);
Config::define('SCRIPT_DEBUG', false);
Config::define('GRAPHQL_DEBUG', false);

ini_set('display_errors', '0');

// Enforce HTTPS
Config::define('FORCE_SSL_ADMIN', true);

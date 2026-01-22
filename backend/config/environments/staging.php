<?php

declare(strict_types=1);

/**
 * Staging Environment Configuration
 *
 * @package Xebia\AuthorityHub
 */

use Roots\WPConfig\Config;

Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', '/tmp/wp-errors.log');
Config::define('DISALLOW_INDEXING', true);

ini_set('display_errors', '0');

<?php

declare(strict_types=1);

/**
 * Xebia Authority Hub - WordPress Configuration
 *
 * This file is required by WordPress but delegates to Bedrock's application.php
 * for all configuration logic. This follows the principle of separation of concerns.
 *
 * @package Xebia\AuthorityHub
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/application.php';
require_once ABSPATH . 'wp-settings.php';

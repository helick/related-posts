<?php
/*
Plugin Name:    Helick Related Posts
Author:         Evgenii Nasyrov
Author URI:     https://helick.io/
*/

// Require Composer autoloader if installed on it's own
if (file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    require_once $composer;
}

// Helpers
require_once __DIR__ . '/src/constants.php';
require_once __DIR__ . '/src/helpers.php';

// Services
\Helick\RelatedPosts\Commands\FlushTransientsCommand::boot();
\Helick\RelatedPosts\CustomFields::boot();

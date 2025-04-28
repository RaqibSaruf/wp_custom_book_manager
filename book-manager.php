<?php

declare(strict_types=1);

/*
 * Plugin Name: Book Manager
 * Description: This is a simple book manager plugin for wordpress.
 * Version: 1.0
 * Author: Saruf <raqibul.dev@gmail.com>
 * 
 */

if (! defined('ABSPATH')) {
    exit;
}


define('BOOK_MANAGER_VERSION', '1.0');
define('BOOK_MANAGER_FILE', __FILE__);
define('BOOK_MANAGER_DIR', __DIR__);
define('BOOK_MANAGER_INCLUDES', BOOK_MANAGER_DIR . '/includes');
define('BOOK_MANAGER_URL', plugin_dir_url(BOOK_MANAGER_FILE));
if(!defined('JWT_SECRET_KEY')) {
    define('JWT_SECRET_KEY', 'book-manager-jwt-secret-key');
}
if (!defined('JWT_ALGORITHM')) {
    define('JWT_ALGORITHM', 'HS256');
}
if(!defined('JWT_EXP_TIME')){
    define('JWT_EXP_TIME', 60 * 60 * 24);
}
/**
 * Register the "book" custom post type
 */

require_once BOOK_MANAGER_DIR . '/vendor/autoload.php';

use Saruf\BookManager\BookManager;


function book_manager_init()
{
    BookManager::getInstance();
}

book_manager_init();

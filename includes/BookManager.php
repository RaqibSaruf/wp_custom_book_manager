<?php

declare(strict_types=1);

namespace Saruf\BookManager;

/** 
 * Final Bookmanager  handler class
 */
final class BookManager
{
    /** 
     * Private static instance variable
     */
    private static $instance;


    /** 
     * private class constructor
     */
    private function __construct()
    {
        $this->init_hooks();
    }


    /**
     * public static instance method
     * @return BookManager
     */
    public static function getInstance(): BookManager
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize hooks.
     * @return void
     */
    private function init_hooks(): void
    {
        register_activation_hook(BOOK_MANAGER_FILE, [$this, 'activate']);
        add_action('plugins_loaded', [$this, 'init_classes']);
        register_uninstall_hook(BOOK_MANAGER_FILE, [self::$instance, 'uninstall']);
    }

    /**
     * Updates info on plugin activation.
     * @return void
     */
    public function activate(): void
    {
        $activator = new Activator();
        $activator->run();
    }


    /** 
     * Updates info on plugin deactivation.
     * @return void
     */
    public function uninstall(): void {
        $uninstaller = new Uninstaller();
        $uninstaller->run();
    }



    /**
     * Initializes the necessary classes for the plugin.
     * @return void
     */
    public function init_classes(): void
    {
        new Frontend();

        if (is_admin()) {
            new Admin();
        }
    }
}

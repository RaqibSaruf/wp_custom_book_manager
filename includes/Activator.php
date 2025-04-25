<?php

declare(strict_types=1);

namespace Saruf\BookManager;

use Saruf\BookManager\Repositories\BookRepository;

/**
 * Plugin activator class.
 */
class Activator
{

    /**
     * Runs the activator.
     * @return void
     */
    public function run(): void
    {
        $this->add_plugin_info();
        $this->create_db_table();
    }

    /**
     * Adds plugin info.
     * @return void
     */
    private function add_plugin_info(): void
    {
        $activated = get_option('book_manager_installation_time');

        if (!$activated) {
            update_option('book_manager_installation_time', time());
        }

        update_option('book_manager_version', BOOK_MANAGER_VERSION);
    }

    /**
     * create database table when activate the plugin
     * @return void
     */
    private function create_db_table(): void
    {
        $repo = new BookRepository();
        $repo->create_table();
    }
}

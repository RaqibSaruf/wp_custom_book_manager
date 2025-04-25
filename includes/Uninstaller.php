<?php

declare(strict_types=1);

namespace Saruf\BookManager;

use Saruf\BookManager\Repositories\BookRepository;

/**
 * Plugin Uninstaller class.
 */
final class Uninstaller
{

    /**
     * Runs the uninstaller.
     * @return void
     */
    public function run(): void
    {
        $this->remove_plugin_info();
        $this->remove_db_table();
    }

    /**
     * Removes plugin info.
     * @return void
     */
    private function remove_plugin_info(): void
    {
        $activated = get_option('book_manager_installation_time');

        if ($activated) {
            delete_option('book_manager_installation_time');
            delete_option('book_manager_version');
        }

    }

    /** 
     * Removes database table
     * @return void
     */
    private function remove_db_table(): void
    {
        $repo = new BookRepository();
        $repo->drop_table();
    }
}

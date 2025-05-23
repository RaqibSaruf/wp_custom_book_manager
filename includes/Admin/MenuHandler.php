<?php
declare(strict_types=1);

namespace Saruf\BookManager\Admin;

/**
 * Menu Handler class
 */
class MenuHandler
{
    /**
     * @var BookHandler
     */
    public $bookHandler;

    /**
     * @var GenreHandler
     */
    public $genreHandler;

    /**
     * @var AuthorHandler
     */
    public $authorHandler;

    /**
     * MenuHandler constructor.
     */
    public function __construct(BookHandler $bookHandler, GenreHandler $genreHandler, AuthorHandler $authorHandler)
    {
        $this->bookHandler = $bookHandler;
        $this->genreHandler = $genreHandler;
        $this->authorHandler = $authorHandler;
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     * @return void
     */
    public function init_hooks(): void
    {
        add_action('admin_menu', [$this, 'add_menu']);
    }

    /**
     * Add menus
     * @return void
     */
    public function add_menu(): void
    {
        add_menu_page('All Books', 'Book Manager', 'manage_options', 'books', [$this->bookHandler, 'book_list'], 'dashicons-book', 20);
        add_submenu_page('books', 'All Books', 'All Books', 'manage_options', 'books', [$this->bookHandler, 'book_list']);
        add_submenu_page('books', 'Add Book', 'Add Book', 'manage_options', 'book-form', [$this->bookHandler, 'book_form']);
        add_submenu_page('books', 'Genres', 'Genres', 'manage_options', 'genres', [$this->genreHandler, 'handle_genres']);
        add_submenu_page('books', 'Authors', 'Authors', 'manage_options', 'authors', [$this->authorHandler, 'handle_authors']);
    }
}
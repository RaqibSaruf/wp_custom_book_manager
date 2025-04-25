<?php
declare(strict_types=1);

namespace Saruf\BookManager\Frontend;

class ShortCode {
    /**
     * @var BookListViewHandler
     */
    public $bookListViewHandler;

    /** 
     * ShortCode class constructor
     */
    public function __construct(BookListViewHandler $bookListViewHandler)
    {
        $this->bookListViewHandler = $bookListViewHandler;
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     * @return void
     */
    public function init_hooks(): void {
        add_shortcode('book_list', [$this->bookListViewHandler, 'render_books']);
    }
}
<?php
declare(strict_types=1);

namespace Saruf\BookManager\Frontend;

use Saruf\BookManager\Repositories\BookRepository;

class BookListViewHandler {

    /**
     * @var BookRepository
     */
    private $bookRepository;
    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Render books method
     */
    public function render_books()
    {
        $books = $this->bookRepository->get_books();
        $output = '<div class="book-list">';
        foreach ($books as $book) {
            $output .= "<div><strong>{$book['name']}</strong> by {$book['author']} ({$book['genre']}) - Rating: {$book['rating']}</div>";
        }
        $output .= '</div>';
        return $output;
    }
}
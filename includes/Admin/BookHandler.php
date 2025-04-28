<?php

declare(strict_types=1);

namespace Saruf\BookManager\Admin;

use Saruf\BookManager\Admin\Tables\BookListTable;
use Saruf\BookManager\Helpers\Template;
use Saruf\BookManager\Repositories\AuthorRepository;
use Saruf\BookManager\Repositories\BookRepository;
use Saruf\BookManager\Repositories\GenreRepository;

/**
 * Book Handler class
 */
class BookHandler
{
    /**
     * @var BookRepository
     */
    private $bookRepository;

    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    /**
     * @var GenreRepository
     */
    private $genreRepository;

    /**
     * Book Handler class constructor
     * @param BookRepository $repo
     */
    public function __construct(BookRepository $repo, AuthorRepository $authorRepository, GenreRepository $genreRepository)
    {
        $this->bookRepository = $repo;
        $this->authorRepository = $authorRepository;
        $this->genreRepository = $genreRepository;
        add_action('admin_post_add_book', array($this, 'save_book'));
        add_action('admin_post_delete_book', array($this, 'delete_book'));
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_media(); // Load WordPress media scripts
        });
    }

    /**
     * Get books method
     * @return void
     */
    public function book_list(): void
    {

        $filter = [];
        if (!empty($_GET['status']) && $_GET['status'] !== 'all') {
            $filter['status'] = $_GET['status'];
        }

        if (!empty($_GET['s'])) {
            $filter['s'] = $_GET['s'];
        }

        $order = [
            'orderby' => $_GET['orderby'] ?? 'name',
            'order' => strtoupper($_GET['order'] ?? 'ASC'),
        ];

        $per_page = 5;
        $page_no = $_GET['paged'] ?? 1;
        $offset = ($page_no - 1) * $per_page;
        $books = $this->bookRepository->get_books($filter, $order, (int)$offset, $per_page);
        $total_items = $this->bookRepository->get_total_count($filter);

        $book_table = new BookListTable($books, (int)$total_items, $per_page);
        $book_table->prepare_items();

        echo Template::render('Admin/Views/index.php', [
            'table' => $book_table,
            'search_id' => 'book_table',
            'action_url' => admin_url('admin.php?page=book-form'),
            'action_label' => 'Add Book'
        ]);
    }

    /**
     * Book delete method
     * @return never
     */
    public function delete_book(): never
    {
        if (isset($_GET['delete'])) {
            $this->bookRepository->delete_book((int)$_GET['delete']);
        }
        wp_redirect(admin_url('admin.php?page=books'));
        exit;
    }

    /**
     * Book form method
     * @return $void
     */
    public function book_form(): void
    {
        $id = $_GET['id'] ?? null;

        $book = $id ? $this->bookRepository->get_book((int)$id) : null;

        $genres = $this->genreRepository->get_all();
        $authors = $this->authorRepository->get_all();

        echo Template::render('Admin/Views/book-form.php', ['book' => $book, 'genres' => $genres, 'authors' => $authors]);
    }

    /**
     * Save Book method
     * @return never
     */
    public function save_book(): never
    {
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'author_id' => (int)$_POST['author_id'],
            'genre_id' => (int)$_POST['genre_id'],
            'publish_date' => date("Y-m-d", strtotime(sanitize_text_field($_POST['publish_date']))),
            'rating' => floatval($_POST['rating']),
            'thumbnail_image' => esc_url_raw($_POST['thumbnail'] ?? ''),
        ];

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

        if ($id) {
            $this->bookRepository->update_book($id, $data);
            wp_redirect(admin_url("admin.php?page=book-form&id=$id"));
        } else {
            $this->bookRepository->add_book($data);
            wp_redirect(admin_url('admin.php?page=books'));
        }
        exit;
    }
}

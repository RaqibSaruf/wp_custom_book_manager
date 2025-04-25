<?php

declare(strict_types=1);

namespace Saruf\BookManager\Admin;

use Saruf\BookManager\Admin\Tables\GenreListTable;
use Saruf\BookManager\Helpers\Template;
use Saruf\BookManager\Repositories\GenreRepository;

/**
 * Book Handler class
 */
class GenreHandler
{
    /**
     * @var GenreRepository
     */
    private $genreRepository;
    /**
     * Book Handler class constructor
     * @param GenreRepository $repo
     */
    public function __construct(GenreRepository $repo)
    {
        $this->genreRepository = $repo;
        add_action('admin_post_add_genre', array($this, 'save_genre'));
        add_action('admin_post_delete_genre', array($this, 'delete_genre'));
    }

    public function handle_genres(): void
    {
        
    }

    /**
     * Get genres method
     * @return void
     */
    public function genre_list(): void
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
            'order' => $_GET['order'] ?? 'ASC',
        ];

        $per_page = 5;
        $page_no = $_GET['paged'] ?? 1;
        $offset = ($page_no - 1) * $per_page;

        $books = $this->genreRepository->get_genres($filter, $order, (int)$offset, $per_page);
        $total_items = $this->genreRepository->get_total_count($filter);


        $book_table = new GenreListTable($books, (int)$total_items, $per_page);
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
            $this->genreRepository->delete_genre((int)$_GET['delete']);
        }
        wp_redirect(admin_url('admin.php?page=books'));
        exit;
    }

    /**
     * Book form method
     * @return $void
     */
    public function genre_form(): void
    {
        $id = $_GET['id'] ?? null;

        $book = $id ? $this->genreRepository->get_genre((int)$id) : null;

        echo Template::render('Admin/Views/form.php', ['book' => $book]);
    }

    /**
     * Save Book method
     * @return never
     */
    public function save_book(): never
    {
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'author' => sanitize_text_field($_POST['author']),
            'genre' => sanitize_text_field($_POST['genre']),
            'publish_date' => date("Y-m-d", strtotime(sanitize_text_field($_POST['publish_date']))),
            'rating' => floatval($_POST['rating']),
            'thumbnail_image' => esc_url_raw($_POST['thumbnail'] ?? ''),
        ];

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

        if ($id) {
            $this->genreRepository->update_genre($id, $data);
            wp_redirect(admin_url("admin.php?page=book-form&id=$id"));
        } else {
            $this->genreRepository->add_genre($data);
            wp_redirect(admin_url('admin.php?page=books'));
        }
        exit;
    }
}

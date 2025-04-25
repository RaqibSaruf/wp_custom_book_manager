<?php

declare(strict_types=1);

namespace Saruf\BookManager\Admin;

use Saruf\BookManager\Admin\Tables\GenreListTable;
use Saruf\BookManager\Helpers\Template;
use Saruf\BookManager\Repositories\GenreRepository;

/**
 * Genre Handler class
 */
class GenreHandler
{
    /**
     * @var GenreRepository
     */
    private $genreRepository;
    /**
     * Genre Handler class constructor
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
        if(isset($_GET['type']) && $_GET['type'] === 'form') {
            $this->genre_form();
        } else {
            $this->genre_list();
        }
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

        $genres = $this->genreRepository->get_genres($filter, $order, (int)$offset, $per_page);
        $total_items = $this->genreRepository->get_total_count($filter);


        $genre_table = new GenreListTable($genres, (int)$total_items, $per_page);
        $genre_table->prepare_items();

        echo Template::render('Admin/Views/index.php', [
            'table' => $genre_table,
            'search_id' => 'genre_table',
            'action_url' => admin_url('admin.php?page=genres&type=form'),
            'action_label' => 'Add Genre'
        ]);
    }

    /**
     * Genre delete method
     * @return never
     */
    public function delete_genre(): never
    {
        if (isset($_GET['delete'])) {
            $this->genreRepository->delete_genre((int)$_GET['delete']);
        }
        wp_redirect(admin_url('admin.php?page=genres'));
        exit;
    }

    /**
     * Genre form method
     * @return $void
     */
    public function genre_form(): void
    {
        $id = $_GET['id'] ?? null;

        $genre = $id ? $this->genreRepository->get_genre((int)$id) : null;

        echo Template::render('Admin/Views/genre-form.php', ['genre' => $genre]);
    }

    /**
     * Save Genre method
     * @return never
     */
    public function save_genre(): never
    {
        $data = [
            'name' => sanitize_text_field($_POST['name']),
        ];

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

        if ($id) {
            $this->genreRepository->update_genre($id, $data);
            wp_redirect(admin_url("admin.php?page=genres&id=$id&type=form"));
        } else {
            $this->genreRepository->add_genre($data);
            wp_redirect(admin_url('admin.php?page=genres'));
        }
        exit;
    }
}

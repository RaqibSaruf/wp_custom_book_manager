<?php

declare(strict_types=1);

namespace Saruf\BookManager\Admin;

use Saruf\BookManager\Admin\Tables\AuthorListTable;
use Saruf\BookManager\Helpers\Template;
use Saruf\BookManager\Repositories\AuthorRepository;

/**
 * Author Handler class
 */
class AuthorHandler
{
    /**
     * @var AuthorRepository
     */
    private $authorRepository;
    /**
     * Author Handler class constructor
     * @param AuthorRepository $repo
     */
    public function __construct(AuthorRepository $repo)
    {
        $this->authorRepository = $repo;
        add_action('admin_post_add_author', array($this, 'save_author'));
        add_action('admin_post_delete_author', array($this, 'delete_author'));
    }

    public function handle_authors(): void
    {
        if(isset($_GET['type']) && $_GET['type'] === 'form') {
            $this->author_form();
        } else {
            $this->author_list();
        }
    }

    /**
     * Get authors method
     * @return void
     */
    public function author_list(): void
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

        $authors = $this->authorRepository->get_authors($filter, $order, (int)$offset, $per_page);
        $total_items = $this->authorRepository->get_total_count($filter);


        $author_table = new AuthorListTable($authors, (int)$total_items, $per_page);
        $author_table->prepare_items();

        echo Template::render('Admin/Views/index.php', [
            'table' => $author_table,
            'search_id' => 'author_table',
            'action_url' => admin_url('admin.php?page=authors&type=form'),
            'action_label' => 'Add Author'
        ]);
    }

    /**
     * Author delete method
     * @return never
     */
    public function delete_author(): never
    {
        if (isset($_GET['delete'])) {
            $this->authorRepository->delete_author((int)$_GET['delete']);
        }
        wp_redirect(admin_url('admin.php?page=authors'));
        exit;
    }

    /**
     * Author form method
     * @return $void
     */
    public function author_form(): void
    {
        $id = $_GET['id'] ?? null;

        $author = $id ? $this->authorRepository->get_author((int)$id) : null;

        echo Template::render('Admin/Views/author-form.php', ['author' => $author]);
    }

    /**
     * Save Author method
     * @return never
     */
    public function save_author(): never
    {
        $data = [
            'name' => sanitize_text_field($_POST['name']),
        ];

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

        if ($id) {
            $this->authorRepository->update_author($id, $data);
            wp_redirect(admin_url("admin.php?page=authors&id=$id&type=form"));
        } else {
            $this->authorRepository->add_author($data);
            wp_redirect(admin_url('admin.php?page=authors'));
        }
        exit;
    }
}

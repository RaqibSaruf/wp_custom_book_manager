<?php

declare(strict_types=1);

namespace Saruf\BookManager\Admin\Tables;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use \WP_List_Table;

class BookListTable extends WP_List_Table
{

    private $books;
    private $perPage;
    private $totalItems;

    public function __construct($books = [], $totalItems = 0, $perpage = 10)
    {

        parent::__construct([
            'singular' => 'Book',
            'plural'   => 'Books',
            'ajax'     => false,
        ]);

        $this->books = $books;
        $this->perPage = $perpage;
        $this->totalItems = $totalItems;

    }

    public function get_columns(): array
    {
        return [
            'cb'           => '<input type="checkbox" />',
            'name'         => 'Book Name',
            'author'       => 'Author',
            'genre'        => 'Genre',
            'publish_date' => 'Publish Date',
            'rating'       => 'Rating',
            'status'       => 'Status',
            'thumbnail_image'    => 'Thumbnail',
            'actions'      => 'Actions',
        ];
    }

    protected function get_bulk_actions()
    {
        return [
	          'delete'       => 'Delete',
	      ];
    }

    protected function get_views()
    {

        $all = admin_url('admin.php?page=books&status=all');
        $active = admin_url('admin.php?page=books&status=active');
        $inactive = admin_url('admin.php?page=books&status=inactive');
        
        $status = $_GET['status'] ?? 'all';

        $allClass = $status === 'all' ? 'class="current"' : '';
        $activeClass = $status === 'active' ? 'class="current"' : '';
        $inactiveClass = $status === 'inactive' ? 'class="current"' : '';
        return [
            'all' => "<a {$allClass} href=\"{$all}\">All</a>",
            'active' => "<a {$activeClass} href=\"{$active}\">Active</a>",
            'inactive' => "<a {$inactiveClass} href=\"{$inactive}\">Inactive</a>",
        ];
    }


    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->set_pagination_args(array(
            'total_items' => $this->totalItems,
            'per_page'    => $this->perPage
        ));

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $this->books;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('name' => array('name', false));
    }


    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="book[]" value="%s" />', esc_attr($item['id']));
    }

    public function column_name($item)
    {
        $edit_url = admin_url("admin.php?page=book-form&id=" . $item['id']);
        return sprintf('<strong><a href="%s">%s</a></strong>', esc_url($edit_url), esc_html($item['name']));
    }

    public function column_thumbnail_image($item) {
        $thumbnail_url = esc_url($item['thumbnail_image'] ?? '');;
        return sprintf('<img src="%s" alt="%s" style="max-width: 60px; height: auto;" />', $thumbnail_url, esc_attr($item['name']));
    }

    public function column_default($item, $column_name)
    {
        return esc_html($item[$column_name] ?? '');
    }

    public function column_actions($item)
    {
        $edit_url = admin_url("admin.php?page=book-form&id=" . $item['id']);
        $delete_url = admin_url("admin-post.php?action=delete_book&delete=" . $item['id']);

        $actions = [
            'edit'   => sprintf('<a href="%s">Edit</a>', esc_url($edit_url)),
            'delete' => sprintf('<a href="%s" class="delete">Delete</a>', esc_url($delete_url)),
        ];

        return implode(' | ', $actions);  // Display the actions
    }

}

<?php

declare(strict_types=1);

namespace Saruf\BookManager\Repositories;

/** 
 * Book Repository class
 */
class BookRepository
{

    /**
     * global $wpdb variable
     */
    private $wpdb;

    /**
     * table name
     */
    private $table_name;

    /**
     * genre table
     */
    private $genre_table;

    /**
     * author table
     */
    private $author_table;

    /**
     * Book Repository Constructor Method
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'books';
        $this->genre_table = $this->wpdb->prefix . 'book_genres';
        $this->author_table = $this->wpdb->prefix . 'book_authors';
    }

    /**
     * Create books table method
     * @return void
     */
    public function create_table(): void
    {
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(256) NOT NULL,
            genre_id BIGINT UNSIGNED NULL,
            author_id BIGINT UNSIGNED NULL,
            publish_date date NULL,
            thumbnail_image VARCHAR(256) NULL,
            rating float NOT NULL DEFAULT 0,
            status VARCHAR(10) NOT NULL DEFAULT 'active',
            PRIMARY KEY  (id),
            FOREIGN KEY (genre_id) REFERENCES {$this->genre_table}(id) ON DELETE SET NULL,
            FOREIGN KEY (author_id) REFERENCES {$this->author_table}(id) ON DELETE SET NULL
        ) {$charset_collate};";

        $this->wpdb->query($sql);
    }


    /** 
     * Drop books table method
     * @return void
     */
    public function drop_table(): void
    {
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->table_name};");
    }

    /** 
     * Add book method
     * @param array $data
     * @return void
     */
    public function add_book(array $data): void
    {
        $this->wpdb->insert($this->table_name, $data);
    }


    /** 
     * Update book method
     * @param int $id
     * @param array $data
     * @return void
     */
    public function update_book(int $id, array $data): void
    {
        $this->wpdb->update($this->table_name, $data, ['id' => $id]);
    }

    /**
     * Delete book method
     * @param int $id
     * @return void
     */
    public function delete_book($id): void
    {
        $this->wpdb->delete($this->table_name, ['id' => $id]);
    }

    /**
     * Get all books method
     * @param array $filter
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function get_books(array $filter = [], array $order = [], $offset = 0, $limit = 10): array
    {

        $condition = $this->get_condition_string($filter);
        $orderRule = $this->get_order_string($order);

        $join = $this->get_join_string();

        $sql = "SELECT {$this->table_name}.*, {$this->genre_table}.name as genre, {$this->author_table}.name as author 
        FROM {$this->table_name} {$join} {$condition} {$orderRule} LIMIT {$limit} OFFSET {$offset}";

        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Get total count method
     * @param array $filter
     * @return int
     */
    public function get_total_count(array $filter = [])
    {
        $condition = $this->get_condition_string($filter);

        $join = $this->get_join_string();

        return $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} {$join} {$condition}");
    }

    private function get_join_string(): string
    {
        return " LEFT JOIN {$this->genre_table} ON {$this->table_name}.genre_id = {$this->genre_table}.id
            LEFT JOIN {$this->author_table} ON {$this->table_name}.author_id = {$this->author_table}.id";
    }

    private function get_order_string(array $order): string
    {
        if (!empty($order)) {
            return 'ORDER BY ' . $order['orderby'] . ' ' . $order['order'];
        }

        return '';
    }

    private function get_condition_string(array $filter): string
    {
        $condition = '';
        if (!empty($filter)) {
            $condition = 'WHERE';
            foreach ($filter as $key => $value) {
                switch ($key) {
                    case 'status':
                        $condition .= " {$this->table_name}.status = '{$value}' AND";
                        break;
                    case 's':
                        $condition .= " ({$this->table_name}.name LIKE '%{$value}%' OR {$this->genre_table}.name LIKE '%{$value}%' OR {$this->author_table}.name LIKE '%{$value}%') AND";
                        break;
                }
            }

            $condition = rtrim($condition, 'AND');
        }

        return $condition;
    }

    /**
     * Get book info method
     * @param int $id
     * @return array
     */
    public function get_book($id): ?array
    {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id), ARRAY_A);
    }
}

<?php

declare(strict_types=1);

namespace Saruf\BookManager\Repositories;

/** 
 * author Repository class
 */
class AuthorRepository
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
     * author Repository Constructor Method
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'book_authors';
    }

    /**
     * Create authors table method
     * @return void
     */
    public function create_table(): void
    {
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(256) NOT NULL,
            status VARCHAR(10) NOT NULL DEFAULT 'active',
            PRIMARY KEY  (id)
        ) {$charset_collate};";

        $this->wpdb->query($sql);
    }


    /** 
     * Drop authors table method
     * @return void
     */
    public function drop_table(): void
    {
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->table_name};");
    }

    /** 
     * Add author method
     * @param array $data
     * @return void
     */
    public function add_author(array $data): void
    {
        $this->wpdb->insert($this->table_name, $data);
    }


    /** 
     * Update author method
     * @param int $id
     * @param array $data
     * @return void
     */
    public function update_author(int $id, array $data): void
    {
        $this->wpdb->update($this->table_name, $data, ['id' => $id]);
    }

    /**
     * Delete author method
     * @param int $id
     * @return void
     */
    public function delete_author($id): void
    {
        $this->wpdb->delete($this->table_name, ['id' => $id]);
    }

    public function get_all(): array 
    {
        return $this->wpdb->get_results("SELECT id , name FROM {$this->table_name} WHERE status = 'active'", ARRAY_A);
    }

    /**
     * Get all authors method
     * @param array $filter
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function get_authors(array $filter = [], array $order = [], $offset = 0, $limit = 10): array
    {

        $condition = $this->get_condition_string($filter);
        $orderRule = $this->get_order_string($order);

        return $this->wpdb->get_results("SELECT * FROM {$this->table_name} {$condition} {$orderRule} LIMIT {$limit} OFFSET {$offset}", ARRAY_A);
    }

    /**
     * Get total count method
     * @param array $filter
     * @return int
     */
    public function get_total_count(array $filter = [])
    {
        $condition = $this->get_condition_string($filter);

        return $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} {$condition}");
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
                        $condition .= " status = '{$value}' AND";
                        break;
                    case 's':
                        $condition .= " name LIKE '%{$value}%' AND";
                        break;
                }
            }

            $condition = rtrim($condition, 'AND');
        }

        return $condition;
    }

    /**
     * Get author info method
     * @param int $id
     * @return array
     */
    public function get_author($id): ?array
    {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id), ARRAY_A);
    }
}

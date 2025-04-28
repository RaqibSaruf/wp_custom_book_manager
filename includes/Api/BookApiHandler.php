<?php
declare(strict_types=1);

namespace Saruf\BookManager\Api;

use Saruf\BookManager\Repositories\BookRepository;
use WP_REST_Request;
use WP_REST_Response;

class BookApiHandler {

    private $bookRepo;
    public function __construct(BookRepository $bookRepo)
    {
        $this->bookRepo = $bookRepo;
    }

    public function get_books(WP_REST_Request $request)
    {
        $params = $request->get_params();
        $filter = [];
        if (!empty($params['status']) && $params['status'] !== 'all') {
            $filter['status'] = $params['status'];
        }

        if (!empty($params['s'])) {
            $filter['s'] = $params['s'];
        }

        $order = [
            'orderby' => $params['orderby'] ?? 'name',
            'order' => strtoupper($params['order'] ?? 'ASC'),
        ];

        $per_page = $params['per_page'] ?? 5;
        $page_no = $params['paged'] ?? 1;
        $offset = ($page_no - 1) * $per_page;
        $books = $this->bookRepo->get_books($filter, $order, (int)$offset, $per_page);
        $total_items = $this->bookRepo->get_total_count($filter);

        $results = [
            'per_page' => (int)$per_page,
            'paged' => (int)$page_no,
            'total_items' => (int)$total_items,
            'total_pages' => ceil($total_items / $per_page),
            'data' => $books
        ];

        return new WP_REST_Response($results, 200);
    }


}
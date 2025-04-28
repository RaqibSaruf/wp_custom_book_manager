<?php

declare(strict_types=1);

namespace Saruf\BookManager;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Saruf\BookManager\Api\AuthenticateApiHandler;
use Saruf\BookManager\Api\BookApiHandler;
use Saruf\BookManager\Repositories\BookRepository;
use WP_Error;

/** Api handler class */
class Api
{

    /**
     * @var BookApiHandler
     */
    public $bookApiHandler;

    /**
     * @var AuthenticateApiHandler
     */
    public $authenticateHandler;

    /** Api class constructor */
    public function __construct()
    {
        $this->bookApiHandler = new BookApiHandler(new BookRepository());
        $this->authenticateHandler = new AuthenticateApiHandler();

        add_filter('determine_current_user', [$this, 'determine_current_user'], 20);

        add_action('rest_api_init', [$this, 'handle_api_routes']);

    }

    public function handle_api_routes() {
        register_rest_route('book-manager/v1', '/login', [
            'methods' => 'POST',
            'callback' => [$this->authenticateHandler, 'login'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('book-manager/v1', '/books', [
            'methods' => 'GET',
            'callback' => [$this->bookApiHandler, 'get_books'],
            'permission_callback' => function () {
                $current_user = wp_get_current_user();
                if ($current_user->ID === 0) {
                    return new WP_Error('unauthorized', 'You are not authorized to access this resource', ['status' => 403]);
                }
                return true;
            },
        ]);
        
    }



    public function determine_current_user($user_id)
    {
        if (!defined('REST_REQUEST') || !REST_REQUEST) {
            return $user_id;
        }

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];

        if (empty($authHeader) || stripos($authHeader, 'Bearer ') !== 0) {
            return new WP_Error('authorization_missing', 'Authorization header missing or incorrect', ['status' => 403]);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        $key = JWT_SECRET_KEY;

        try {
            $decoded = JWT::decode($token, new Key($key, JWT_ALGORITHM));

            if ($decoded->exp < time()) {
                return new WP_Error('token_expired', 'Token has expired', ['status' => 403]);
            }

            $user_id = (int) $decoded->data->user->id ?? 0;

            return $user_id;
        } catch (\Exception $e) {
            return new WP_Error('invalid_token', 'Invalid or malformed token', ['status' => 403]);
        }
    }
}

<?php
declare(strict_types=1);

namespace Saruf\BookManager\Api;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class AuthenticateApiHandler {
    public function login(WP_REST_Request $request) {
        $username = sanitize_text_field($request->get_param('username'));
        $password = $request->get_param('password');

        if (empty($username) || empty($password)) {
            return new WP_Error('missing_fields', 'Username and Password required', ['status' => 422]);
        }

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            return new WP_Error('invalid_credentials', 'Invalid Username or Password', ['status' => 403]);
        }
        $key = JWT_SECRET_KEY;
        $issuedAt = time();
        $expirationTime = $issuedAt + JWT_EXP_TIME;
        $payload = [
            'iss' => BOOK_MANAGER_URL,
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => [
                'user' => [
                    'id' => $user->ID,
                ]
            ]
        ];

        $jwt = JWT::encode($payload, $key, JWT_ALGORITHM);

        return new WP_REST_Response([
            'token' => $jwt
        ], 201);
    }
}
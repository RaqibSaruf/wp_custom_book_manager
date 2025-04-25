<?php

declare(strict_types=1);

namespace Saruf\BookManager\Helpers;

use RuntimeException;

class Template
{

    /**
     * Render a template file
     * @param string $file_path
     * @param array $data
     * @return string
     */
    public static function render(string $file_path, array $data = []): string
    {
        $file_path = ltrim($file_path, BOOK_MANAGER_INCLUDES);
        $file_path = ltrim($file_path, '/');
        $file_path = BOOK_MANAGER_INCLUDES . "/" . $file_path;
        if (file_exists($file_path)) {
            extract($data);
            ob_start();
            require $file_path;
            $content = ob_get_clean();

            return $content;
        } else {
            throw new RuntimeException('View file not found');
        }
    }
}

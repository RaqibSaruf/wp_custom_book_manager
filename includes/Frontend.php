<?php

declare(strict_types=1);

namespace Saruf\BookManager;

use Saruf\BookManager\Frontend\BookListViewHandler;
use Saruf\BookManager\Frontend\ShortCode;
use Saruf\BookManager\Repositories\BookRepository;

/**
 * Frontend handler class.
 */
class Frontend
{

    /**
     * Frontend class constructor.
     */
    public function __construct()
    {
        new ShortCode(new BookListViewHandler(new BookRepository()));
    }
}

<?php

declare(strict_types=1);

namespace Saruf\BookManager;

use Saruf\BookManager\Admin\AuthorHandler;
use Saruf\BookManager\Admin\BookHandler;
use Saruf\BookManager\Admin\GenreHandler;
use Saruf\BookManager\Admin\MenuHandler;
use Saruf\BookManager\Repositories\AuthorRepository;
use Saruf\BookManager\Repositories\BookRepository;
use Saruf\BookManager\Repositories\GenreRepository;

/** Admin handler class */
class Admin
{

    /** Admin class constructor */
    public function __construct()
    {
        $bookHandler = new BookHandler(new BookRepository());
        $genreHandler = new GenreHandler(new GenreRepository());
        $authorHandler = new AuthorHandler(new AuthorRepository());
        new MenuHandler($bookHandler, $genreHandler, $authorHandler);
    }
    
}

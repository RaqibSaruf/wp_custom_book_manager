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
        $authorRepo = new AuthorRepository();
        $genreRepo = new GenreRepository();
        $bookRepo = new BookRepository();
        
        $bookHandler = new BookHandler($bookRepo, $authorRepo, $genreRepo);
        $genreHandler = new GenreHandler($genreRepo);
        $authorHandler = new AuthorHandler($authorRepo);

        new MenuHandler($bookHandler, $genreHandler, $authorHandler);
    }
    
}

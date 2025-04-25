<?php

declare(strict_types=1);


if(!function_exists('dd')){
    function dd(...$args){
        echo '<pre>';
        foreach($args as $arg){
            var_dump($arg);
            echo '<br/> =============================== <br/>';
        }
        echo '</pre>';
        die;
    }
}
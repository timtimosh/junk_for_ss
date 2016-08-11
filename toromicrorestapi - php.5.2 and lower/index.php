<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require("../../include/config.php"); 
require("lib/auth.php"); 
#todo use autoload motherfucka :(
require("handlers/comment_handler.php");
require("handlers/comments_handler.php");
require("handlers/fastbuy_handler.php");
require("handlers/fastbuys_handler.php");
require("lib/markdown.php");
require("lib/mysql.php");

/**
 *@todo заменить это
 */

    MySQL::create($dbhost,$dbname,$dbusername,$dbpassword);

require("src/Toro.php");

/**
 *@todo заменить это ... ошибками на нормальные обработчики
 */
ToroHook::add("404", 'error404');

function error404($opts){
    echo 'no result';
    exit();
}

ToroHook::add("401", 'errorAuthentificate');

function error401(){
    echo 'Сначала авторизируйтесь'; 
    exit();
}


// Before/After callbacks in order
//ToroHook::add("before_request", function() {});
//ToroHook::add("before_handler", function() {});
//ToroHook::add("after_handler", function() {});

ToroHook::add("after_request",  'after_request');

function after_request($result){
     echo json_encode($result['result']);
}

 
Toro::serve(array(
    ///"/" => "ArticlesHandler",
   // "/article/:alpha" => "ArticleHandler",
    /**
     * get comment by id get_xhr if get method
     * del comment by id delete_xhr if delete method and so on
     */
    "/comment/" => "CommentHandler",
    "/comment/:number" => "CommentHandler",
    "/fastbuy/:number" => "FastbuyHandler",
    "/fastbuy/" => "FastbuyHandler",
    "/fastbuy/list" => "FastbuysHandler",
       /**
     * get comments by article_id get_xhr. if you need to get comments with limits, please use from-to. 
     * @example http://ToroMicroRestApi/article/502/comments/1-10. will take comments from 1 to 10. 
     * 
     */
    
    "/article/:number/comments/:alpha" => "CommentsHandler",
));



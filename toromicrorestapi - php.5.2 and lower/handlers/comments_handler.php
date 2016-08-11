<?php
require_once("model/comment.php");

class CommentsHandler {
    
    private $_model;
    
    public function __construct() {
        $this->_model = new Comment;
    }
    
    
    function get_xhr($article_id,$limits) {
        return $this->_model->get_comments($article_id);
    }
    
    
}
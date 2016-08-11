<?php
require_once("model/comment.php");


class CommentHandler {
    private $_model;
    
    public function __construct() {
        $this->_model = new Comment;
    }
    
    public function del_xhr($comment_id) {
       return $this->_model->delete($comment_id);  
    }
    
    public function put_xhr($comment_id) {
        $put=Request::getPost();
     
        return $this->_model->update($comment_id,$put);
    }
    
    public function get_xhr($comment_id) {
        return $this->_model->get($comment_id);
    }
    
    public function post_xhr() {
       $post=Request::getPost();
       return $this->_model->save($post); 
    }
    
    public function delete_xhr($comment_id) {
       return $this->_model->delete($comment_id); 
    }
}
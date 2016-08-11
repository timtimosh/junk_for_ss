<?php
require_once("model/fastbuy.php");

class FastbuyHandler {
    private $_model;
    
    public function __construct() {
        $this->_model = new FastBuy;
    }
    
    public function del_xhr($opts) {
      return $this->_model->delete($comment_id); 
    }
    
    public function put_xhr($fastbuy_id) {
        $put=Request::getPost();
     
        return $this->_model->update($fastbuy_id,$put);
    }
    
    public function get_xhr() {
        return $this->_model->get($fastbuy_id);
    }
    
    public function post_xhr() {
      
       $post=Request::getPost();
       
       return $this->_model->save($post); 
    }
    
    public function delete_xhr($comment_id) {
       return $this->_model->delete($comment_id); 
    }
}
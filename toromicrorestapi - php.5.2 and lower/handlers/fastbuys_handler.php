<?php
require_once("model/fastbuy.php");

class FastbuysHandler {
    
    private $_model;
    
    public function __construct() {
        $this->_model = new Fastbuy;
    }
    
    
    function get_xhr() {
        return $this->_model->get_all();
    }
    
    
}
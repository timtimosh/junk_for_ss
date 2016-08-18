<?php

class Request {

    public $get = array();
    public $post = array();
    public $cookie = array();
    public $files = array();
    public $server = array();
    public $default_lang = 'ru';

    public function __construct() {
        $this->get = $this->clean($_GET);
        $this->post = $this->clean($_POST);
        $this->request = $this->clean($_REQUEST);
        $this->cookie = $this->clean($_COOKIE);
        $this->files = $this->clean($_FILES);
        $this->server = $this->clean($_SERVER);
    }

    public function lang_detect() {
        global $db;
        $languages = array();
        $query = $db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE status = '1'");

        foreach ($query->rows as $result) {
            $languages[$result['code']] = $result;
        }

        $lang_code_was_set = 0;
        /**
         * TODO DOUBLE CODE :(
         */
       
        
        if (!empty($this->get['_route_']) && strlen($this->get['_route_']) > 0) {
            $parts = explode('/', $this->get['_route_']);
            foreach ($languages as $lang_code => $lang) {
                if ($lang_code == $parts[0]) {
                    $lang_code_was_set = $lang_code;
                    if(count($parts)==1){
                        $this->get['_route_'] = str_replace($parts[0], "", $this->get['_route_']);    
                    }
                    else $this->get['_route_'] = str_replace($parts[0] . '/', "", $this->get['_route_']);
                        
                    break;
                }
            }
        }
      
      
   
        
        if (!empty($this->get['route']) && strlen($this->get['route']) > 0) {
            

                $parts = explode('/', $this->get['route']);
               
                if(strpos($_SERVER["REQUEST_URI"],"/ua/")!==false && $parts[0]!='ua'){
                    $this->get['route']='ua/'.$this->get['route'];
                }
               
           
            $parts = explode('/', $this->get['route']);
           

                
            foreach ($languages as $lang_code => $lang) {
                if ($lang_code == $parts[0]) {
                    $lang_code_was_set = $lang_code;
                    if(count($parts)==1){
                        $this->get['route'] = str_replace($parts[0], "", $this->get['route']);    
                    }
                    else $this->get['route'] = str_replace($parts[0] . '/', "", $this->get['route']);

                    break;
                }
            }
        }
        
     
        

        if (empty($lang_code_was_set)) {
            $lang_code_was_set = $this->default_lang;
        }

        $this->lang_set($languages, $lang_code_was_set);
    }

    private function lang_set($languages, $code) {
        global $registry;

        if (!isset($registry->get('session')->data['language']) || $registry->get('session')->data['language'] != $code) {
            $session = $registry->get('session');
            $session->data['language'] = $code;
        }

        if (!isset($this->cookie['language']) || $this->cookie['language'] != $code) {
            setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $this->server['HTTP_HOST']);
        }

        $registry->get('config')->set('config_language_id', $languages[$code]['language_id']);
        $registry->get('config')->set('config_language', $languages[$code]['code']);

// Language
        $language = new Language($languages[$code]['directory']);
        $language->load($languages[$code]['directory']);
        $registry->set('language', $language);
    }

    public function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }

}

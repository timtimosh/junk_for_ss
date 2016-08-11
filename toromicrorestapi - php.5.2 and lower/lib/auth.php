<?php

 class Auth{
 
    private function check_auth($user,$pass) {
        /* replace with appropriate username and password checking,
           such as checking a database */
        $users = array('user' => 'pass',
                       'user2'  => 'pass');

        if (isset($users[$user]) && ($users[$user] == $pass)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function is_admin(){
     list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = 
  explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
     
     
        if (!self::check_auth($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
            throw new Exception("You need to enter a valid username and password you entered: ".$_SERVER['PHP_AUTH_USER'].":".$_SERVER['PHP_AUTH_PW'], 401);
        }

    }
 }


<?php

class Comment{
    
    private $_columns=array("article_id"=>"", "minuses"=>"", "pluses"=>"", "body"=>"", "rate"=>"", "name"=>"", "status"=>0);
      
    public function get($comment_id) {
        
        $stmt = MySQL::getInstance()->prepare("SELECT * FROM comments where id =:comment_id");
        $stmt->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        return $comment;
    }
    
    public function update($comment_id,$data){
        
        Auth::is_admin();
        
        $data = $this->filter_before_save($data);
        

        
        $stmt = MySQL::getInstance()->prepare('UPDATE comments SET body = :body, minuses = :minuses, pluses = :pluses, rate = :rate, name = :name, status = :status WHERE id = :comment_id LIMIT 1');
        
        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_INT);
        $stmt->bindParam(':body', $data['body'], PDO::PARAM_STR);
        $stmt->bindParam(':minuses', $data['minuses'], PDO::PARAM_STR);
        $stmt->bindParam(':pluses', $data['pluses'], PDO::PARAM_STR);
        $stmt->bindParam(':rate', $data['rate'], PDO::PARAM_INT);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
        
        
        $stmt->execute();
        
        return $this->get($comment_id);
    }
    
    public function save($data){
        

        $data = $this->filter_before_save($data);
        
       
        if (empty($data['article_id'])) {
            throw new Exception("article_id missing");
        }
        
        $article_id = $data['article_id'];
               $ip=Request::get_user_ip();
               
        $stmt = MySQL::getInstance()->prepare('INSERT INTO comments SET article_id =:article_id, '
                . 'body = :body, minuses = :minuses, pluses = :pluses, '
                . 'rate = :rate, name = :name, ip = :ip');
        
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_INT);
        $stmt->bindParam(':body', $data['body'], PDO::PARAM_STR);
        $stmt->bindParam(':minuses', $data['minuses'], PDO::PARAM_STR);
        $stmt->bindParam(':pluses', $data['pluses'], PDO::PARAM_STR);
        $stmt->bindParam(':rate', $data['rate'], PDO::PARAM_INT);
         $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
         
        $stmt->execute();
        return $this->get(MySQL::getInstance()->lastInsertId());
    }
    
    public function delete($comment_id) {
        Auth::is_admin();
              
        $stmt = MySQL::getInstance()->prepare("DELETE FROM comments where id =:comment_id LIMIT 1");
        $stmt->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);
        $result=$stmt->execute();

        return $result;
    }
    
    public function get_comments($article_id) {
        $stmt = MySQL::getInstance()->prepare("SELECT * FROM comments where article_id =:article_id and status = 1 order by id DESC");
        $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute();
        $comment = $stmt->fetchAll();
        return $comment;
    }

    private function filter_before_save($data){
        
        foreach ($this->_columns as $key => $value) {
            if (empty($data[$key])) {
                $data[$key]=$value;
            }
            
             $data[$key]= strip_tags($data[$key]);
        }
        
        return $data;
    }
    
  
}

<?php

class FastBuy {

    private $_columns = array("phone" => "", "product_name" => "", "product_url" => "", "status" => 0, "address"=>"", "fio"=>"", "bike"=>"");

    public function get($fast_buy_id) {

        $stmt = MySQL::getInstance()->prepare("SELECT * FROM fastbuy where id =:fast_buy_id");
        $stmt->bindValue(':fast_buy_id', $fast_buy_id, PDO::PARAM_INT);
        $stmt->execute();
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        return $comment;
    }

    public function update($fast_buy_id, $data) {

        Auth::is_admin();

        $data = $this->filter_before_save($data);


        $stmt = MySQL::getInstance()->prepare('UPDATE fastbuy SET phone =:phone, '
                . 'address = :address, bike = :bike, fio = :fio, status = :status  WHERE id = :fast_buy_id LIMIT 1');

        $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
        $stmt->bindParam(':address', $data['address'], PDO::PARAM_STR);
        $stmt->bindParam(':bike', $data['bike'], PDO::PARAM_STR);
        $stmt->bindParam(':fio', $data['fio'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_INT);
        $stmt->bindParam(':fast_buy_id', $fast_buy_id, PDO::PARAM_INT);
        $stmt->execute();

        
        foreach ($data['item'] as $key => $order_items) {
             
                $stmt = MySQL::getInstance()->prepare('UPDATE fastbuy_items SET product_url =:product_url, '
                . 'product_name = :product_name, qty = :qty  WHERE id = :id LIMIT 1');

             
                $stmt->bindParam(':product_name', $order_items['product_name'], PDO::PARAM_STR);
                $stmt->bindParam(':product_url', $order_items['product_url'], PDO::PARAM_STR);
                $stmt->bindParam(':qty', $order_items['qty'], PDO::PARAM_INT);
                $stmt->bindParam(':id', $order_items['id'], PDO::PARAM_INT);
                $stmt->execute();
        
        }
        
        
        return $this->get($fast_buy_id);
    }

    public function save($data) {
        
        
        
        $data = $this->filter_before_save($data);

        $stmt = MySQL::getInstance()->prepare('INSERT INTO fastbuy SET phone =:phone, '
                . 'address = :address, bike = :bike, fio = :fio');

        $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
        $stmt->bindParam(':address', $data['address'], PDO::PARAM_STR);
        $stmt->bindParam(':bike', $data['bike'], PDO::PARAM_STR);
        $stmt->bindParam(':fio', $data['fio'], PDO::PARAM_STR);
        
        if(!$stmt->execute()){
          return 'Извините. Произошла ошибка, попробуйте перезвонить нам!';
        }
         
        $order_id=MySQL::getInstance()->lastInsertId();
        
        if ($order_id>0) {
            
            foreach ($data['item'] as $key => $order_items) {
             
                $stmt = MySQL::getInstance()->prepare('INSERT INTO fastbuy_items SET product_url =:product_url, '
                . 'product_name = :product_name, order_id = :order_id, qty = :qty');

                $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                $stmt->bindParam(':product_name', $order_items['product_name'], PDO::PARAM_STR);
                $stmt->bindParam(':product_url', $order_items['product_url'], PDO::PARAM_STR);
                $stmt->bindParam(':qty', $order_items['qty'], PDO::PARAM_STR);
                $stmt->execute();
        
            }
        }
        else {
             return 'Извините. Произошла ошибка, попробуйте перезвонить нам!';
        }
    
            $this->send_email_to_admin($data);
            
            return 'Спасибо Ваша заявка принята! Наши менеджеры свяжуться с Вами в ближайшее время!';

        //return $this->get(MySQL::getInstance()->lastInsertId());
    }

    public function delete($fast_buy_id) {
        Auth::is_admin();

        $stmt = MySQL::getInstance()->prepare("DELETE FROM fastbuy where id =:fast_buy_id LIMIT 1");
        $stmt->bindValue(':fast_buy_id', $fast_buy_id, PDO::PARAM_INT);
        $result = $stmt->execute();

        return $result;
    }

    public function get_all() {
        Auth::is_admin();
        $stmt = MySQL::getInstance()->prepare("SELECT * FROM fastbuy order by status ASC, id DESC");
        $stmt->execute();
        $f = $stmt->fetchAll();
        return $f;
    }

    private function filter_before_save($data) {

        foreach ($this->_columns as $key => $value) {
            if (empty($data[$key])) {
                $data[$key] = $value;
            }

            $data[$key] = strip_tags($data[$key]);
        }

        return $data;
    }

    private function send_email_to_admin($data = array()) {
        $user = 'sales@simplyroad.ua';
        $password = 'SR082016';
        $send_email_url = 'https://esputnik.com/api/v1/message/email';

        $from = '"Ростислав" <sales@simplyroad.ua>'; // отправитель в формате "Имя" <email@mail.com>
        $subject = 'Новый заказ поступил';
        
        $message="Клиент хочет купить товары. <br>Номер телефона ".$data['phone'];
 
        if($data['address']!='') $message.="<br>Адрес: ".$data['address'];
        if($data['fio']!='') $message.="<br>Фио: ".$data['fio'];
        if($data['bike']!='') $message.="<br>Мотоцикл: ".$data['bike'];

        foreach ($data['item'] as $key => $order_items) {         
               $message.= '<br>Название: '.$order_items['product_name'];
               $message.= '<br>Ссылка: '.$order_items['product_url'];
               $message.= '<br>К-во: '.$order_items['qty'];
        }
        
        
        $htmlText = "<html><body><strong>Новый заказ!</strong><br>$message</body></html>";
        
        $plainText = str_replace("<br>", "\n", $message); // вариант письма в виде простого текста
      
        
        
        $emails = array('sales@simplyroad.ua');

        $json_value = new stdClass();
        $json_value->from = $from;
        $json_value->subject = $subject;
        $json_value->htmlText = $htmlText;
        $json_value->plainText = $plainText;
        $json_value->emails = $emails;

       

//        
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_value));
//            curl_setopt($ch, CURLOPT_HEADER, 1);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
//            curl_setopt($ch, CURLOPT_URL, $send_email_url);
//            curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $password);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            $output = curl_exec($ch);
//            curl_close($ch);
//            $info = curl_getinfo($ch); // выдаст сформированные аут-хеадеры 
            $context = stream_context_create(array(
                'http' => array(
                    'method'  => 'POST',
                         'header' => "Authorization: Basic " . base64_encode("$user:$password")
                                    . "Accept: application/json\r\n"
                                    . "Content-Type: application/json\r\n", 



                    'content' => json_encode($json_value)
                )
            ));

            file_get_contents($send_email_url, false, $context);

            

    }

}

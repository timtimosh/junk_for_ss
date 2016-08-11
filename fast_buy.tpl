<form id="quickOrderForm" class="form" action="#" method="post">
    <input type="phone" id="phone"  name="phone" maxlength="13" required="required" placeholder="Введите Ваш телефон">
   
    <input type="hidden" name="item[0][product_name]" value="{php}$titpage = $this->get_template_vars('titpage'); echo $titpage['text_name'];{/php}">
 

    <input type="hidden" name="item[0][qty]" value="1">
    <input type="hidden" name="item[0][product_url]" value="{php}echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";{/php}">

    <button id="quickOrderSubmit" type="submit" class="fastbuy_btn">Купить в один клик</button>
</form>
{literal}
   <script type="text/javascript">
        <!--//<![CDATA[
        var phone_callback = {
            
            
           phone_el: $("#phone"),
           phone_start_val: "+380",
           url: "/ss/toromicrorestapi/fastbuy/",
           pl_form: $("#quickOrderForm"),

           success: function(html){
                $('#phone').notify(html, "success");
           },

            error: function(html){
              
                $('#phone').notify(html, "error");
            },

            validator: function(){
                    
                   
                
                    var str = new String(this.phone_el.val());

                    if (/^\+380\d{9}$/.test(str)) {
                        return true;
                    } else {
                        this.phone_el.focus();
                        return false;
                    }

            },
            run: function(event){
                event.preventDefault();
                
                if (this.validator()) {
                    var data= this.pl_form.serialize();             
                         
                            
                            
                    this.ajax_send(this.url, data, this.success, this.error);
                }
                else {
                   this.error("Введите телефон правильно. Например +380994995634");
                }
                
            },
            init: function(){
                this.phone_el.val(this.phone_start_val);
                this.pl_form.submit(function (e) {
                    phone_callback.run(e);
                });
                
               
                this.phone_el.focus(function () {
                /*if (this.value != '') this.select();
                else{
                        this.value = '+380'; 
                    }*/
                }).blur(function () {
                if (this.value == '+380')
                        this.value = '';
                else if (this.value.length)
                        this.value = this.value.replace(/[^\d\+]/g, '');
                });
            },
            ajax_send: function (url, data, callback_success, callback_error){
                $.ajax({
                        type: "POST",
                        url: url,
                        dataType: 'json',
                        data: data,
                        success: function(data){
                            console.log(data);
                            if (data.error) {
                                // handle the error
                                callback_error(data.error.msg);
                                    
                            }
                            
                            else {
                               callback_success(data);
                               phone_callback.phone_el.val(phone_callback.phone_start_val);
                            }
                                
                        }, 
                        
                        error: callback_error,
                });
            }
        };
            phone_callback.init();     
            //]]>-->
    </script>

{/literal}


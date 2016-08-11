
<form id="addComment" class="new_comment comment_row">

    <input name="article_id" type="hidden" value="<?php echo $article_id;?>">

    <div class="user_logo user_comment_avatar" data-user_id="" data-size="200" style="background-image: url('http://www.moyo.ua/img/anonimus.jpg')">
    </div>
    <div class="remainingText">

        <textarea name="body" id="commentText"  minlength="20" maxlength="500" required class="main_area" placeholder="Оставьте ваш отзыв или задайте вопрос эксперту" style="height: 82px;"></textarea>
        <div class="textareaFeedback">500</div>
    </div>

    <div class="arrow"></div>
    <div class="comment_hidden">
        
    <div class="stars_newcomment" style="opacity: 1;">
        <div id="commentRating" class="rankingStars clear_after">
            <span>Оцените продукт</span>
            <select required name="rate" id="rate_product">
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
        </div>

    </div>
        
        <input name="name" maxlength="30" minlength="3" id="name" required type="text" placeholder="Ваше имя">


        <div class="plus_minus">Укажите плюсы и минусы модели</div>
        <div class="plusminus_inputs">
            <div class="remainingText"><textarea minlength="10" name="pluses" id="commentPluses" maxlength="500" placeholder="Плюсы"></textarea><div class="textareaFeedback">500</div></div>
            <div class="remainingText"><textarea  minlength="10" name="minuses" maxlength="500" placeholder="Минусы"></textarea><div class="textareaFeedback">500</div></div>
        </div>

        <input id="commentSubmit" type="submit" value="Оставить отзыв">
    </div>
</form>


    <script type="text/javascript">
        var add_comment = {
           url: "/ss/toromicrorestapi/comment/",
           form: $("#addComment"),
           add_comment_text: $("#commentText"),
           plus_minus_inputs: $('.plusminus_inputs'),
           plus_minus_button: $('.plus_minus'),
       
           
           success: function(html){
               $('#commentSubmit').notify("Спасибо за отзыв! Он будет добавлен после проверки администратором.", "success");
           },

            error: function(html){
                $('#commentSubmit').notify("Произошла ошибка, сообщите администратору", "error");
            },

            validator: function(){
                    
                   return true;

            },
            run: function(event){
                event.preventDefault();
                
                if (this.validator()) {
                    this.ajax_send('POST', this.url, this.form.serialize(), this.success);
                }
                else {
                    console.log("error add comment validation");
                }
                
            },
            init: function(){
                $('.comment_hidden').hide();
                this.plus_minus_inputs.hide();
                
                this.form.submit(function (e) {
                    add_comment.run(e);
                });
                 this.plus_minus_button.click(function (e) {
                     add_comment.plus_minus_inputs.toggle('fast');
                });
               
                this.add_comment_text.focus(function () {
                   $('.comment_hidden').show('fast');
                });
            },
            ajax_send: function (type, url, data, callback_success){
                $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        success: function (html) {
                        //console.log("from ajax_send", html);
                            callback_success(html);
                        },
                });
            }
        };
        add_comment.init();     
    </script>




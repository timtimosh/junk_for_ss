<div class="comments-ss">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/ss/toromicrorestapi/view/css/style.css">
    <style>.notifyjs-bootstrap-success{text-align:left;}</style>
    <?php include("add_comment.php"); ?>

    <p><strong>Комментариев (<?php echo count($comments); ?>)</strong></p>

    <?php
    foreach ($comments as $comment) {
        ?>
  
        <div class="comment_row " itemprop="review" itemscope="" itemtype="http://schema.org/Review">
            <meta itemprop="name" content="<?php echo $page . text_name ?>">
            <div class="user_logo user_comment_avatar" data-user_id="" data-size="200" style="background-image: url(/ss/toromicrorestapi/view/img/anonimus.jpg)">
            </div>
            <div class="author-name">
                <span class="bold" itemprop="author"><?php echo $comment->name ?></span>
                <div class="rankingStars clear_after" data-rating="4">
                    <span class="starRating">
                        <?php
                        for ($i=5; $i>=1; $i--){ $label = '';
                            if( $comment->rate<$i){ $label = 'not_active';}
                            echo '<input id="rating'.$i.'" type="radio" name="rating" value="'.$i.'">
                        <label for="rating'.$i.'" class="'.$label.'"></label>';
                        }
                        ?>
                      
                    </span>

                </div>
                <div class="comment-date"><?php echo $comment->posted ?></div>
            </div>
            <div class="hidden_comment" itemprop="reviewRating" itemscope="" itemtype="http://schema.org/Rating">
                <meta itemprop="worstRating" content="0">
                <meta itemprop="bestRating" content="5">
                <meta itemprop="ratingValue" content="<?php echo $comment->rate ?>">
            </div>
            <div class="text" itemprop="reviewBody">
                <?php echo $comment->body; ?>

            </div>
            <?php if ($comment->pluses != ''): ?>
                <div class="benefits"><span class="title">Достоинства:</span>
                    <?php echo $comment->pluses; ?>
                </div>
            <?php endif; ?>
            <?php if ($comment->minuses != ''): ?>
                <div class="lacks">
                    <span class="title">Недостатки:</span>
                    <?php echo $comment->minuses; ?>
                </div>
            <?php endif; ?>
        </div>



        <?php
    }
    ?>


</div>
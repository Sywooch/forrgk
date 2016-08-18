<?php

use yii\helpers\Html;
?>
<h1><?php echo Html::encode($article->title)?></h1>
<br />
<?php echo $article->content;?>
<p class="text-info bg-info">
    <?php echo 'Опубликовано ', (new \DateTime($article->post_date))->format('d.m.Y в H:i');?>
</p>

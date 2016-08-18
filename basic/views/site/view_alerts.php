<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<h1>Просмотр уведомлений</h1>
<div class="panel panel-default">
    <div class="panel-body">



        <?php foreach ($alerts['items'] as $alert):?>
        <div class="alert <?php if ($alert['viewed'] == 0) echo 'alert-success'; else echo 'alert-info';?>">
            <div class="pull-left">
                <h4>Уведомление: <?php echo Html::encode($alert['title']);?> от <?php echo ($alert['from_user'] == 0 ? 'Системы' : Html::encode($alert['from_username']))?> Дата: <?php echo (new \DateTime($alert['post_date']))->format('d.m.Y в H:i')?>
                <?php if ($alert['to_user'] == 0) echo '<span class="label label-warning">Широковещательное</span>';?>
                </h4>


                <?php echo Html::encode($alert['content']);?>
            </div>
            <div class="pull-right">
                <?php if ($alert['viewed'] == 0):?>
                <a href="<?php echo Url::to(['site/switch'])?>&id=<?php echo $alert['id']?>&page=<?php echo $alerts['page'];?>" class="btn btn-success">
                    <i class="fa fa-bookmark"></i> Прочитано
                </a>
                <?php endif;?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php endforeach;?>

        <?php if ($alerts['pages'] > 1):?>
        <div class="text-center">
            <ul class="pagination">
                <?php for ($i=1;$i<=$alerts['pages'];++$i):?>
                    <li<?php if ($i == $alerts['page']) echo ' class="active"';?>>
                        <a href="<?php echo Url::to(['site/alerts'])?>&page=<?php echo $i?>">
                            <?php echo $i;?>
                        </a>
                    </li>
                <?php endfor;?>
            </ul>
        </div>
        <?php endif;?>
    </div>
</div>
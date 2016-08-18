<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
?>


<h1>Управление плагинами</h1>
<p>
    Если Вы хотите добавить плагин, то добавьте его в папку <code>basic/plugins</code>
</p>


<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>
                #
            </th>
            <th>
                Название
            </th>
            <th>
                Состояние
            </th>
            <th>
                Действия
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($plugins as $num=>$plugin):?>
        <tr>
            <td>
                <?php echo $num+1;?>
            </td>
            <td>
                <?php echo Html::encode($plugin['name']);?>
            </td>
            <td>
                <?php if ($plugin['enabled']):?>
                    <span class="label label-success">включён</span>
                <?php else:?>
                    <span class="label label-default">отключён</span>
                <?php endif;?>
            </td>
            <td>
                <?php if ($plugin['enabled']):?>
                    <a href="<?php echo Url::to(['pages/plugins/switch']);?>&name=<?php echo Html::encode($plugin['name']);?>" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Отключить
                    </a>
                <?php else:?>
                    <a href="<?php echo Url::to(['pages/plugins/switch']);?>&name=<?php echo Html::encode($plugin['name']);?>" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i> Включить
                    </a>
                <?php endif;?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$plugins): ?>
        <tr>
            <td class="text-center text-muted" colspan="4">
                Плагинов не было обноаружено
            </td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
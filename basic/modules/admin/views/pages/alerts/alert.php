<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;
$id = !isset($id) ? 0 : $id;

$ulist = User::getAll();
?>

<h1><?php if ($id > 0) echo 'Редактирование уведомления'; else echo 'Создание уведомления';?></h1>
<h3>
    <?php if ($id > 0):?>
        <?php echo Html::encode($alert->name);?>
    <?php else:?>
        Новое уведомление
    <?php endif;?>
</h3>
<br />



<form action="<?php echo Url::toRoute('write');?>&id=<?php echo $id;?>" method="POST" class="form-horizontal">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
    <div class="form-group">
        <div class="col-sm-5">
            <b>Название:</b>
        </div>
        <div class="col-sm-7">
            <input type="text" class="form-control" name="name" value="<?php echo Html::encode($alert->name)?>">
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-5">
            <b>От кого:</b>
        </div>
        <div class="col-sm-7">
            <select name="from_user" class="form-control">
                <option value="0">Не указано</option>
                <?php foreach ($ulist as $u) : ?>
                    <option value="<?php echo $u['id']?>"<?php if ($u['id'] == $alert->from_user) echo ' selected';?>><?php echo Html::encode($u['username']);?></option>
                <?php endforeach; unset($u);?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-5">
            <b>Кому:</b>
        </div>
        <div class="col-sm-7">
            <select name="to_user" class="form-control">
                <option value="0">Всем пользователям</option>
                <?php foreach ($ulist as $u) : ?>
                    <option value="<?php echo $u['id']?>"<?php if ($u['id'] == $alert->to_user) echo ' selected';?>><?php echo Html::encode($u['username']);?></option>
                <?php endforeach; unset($u);?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-5">
            <b>Заголовок:</b>
        </div>
        <div class="col-sm-7">
            <input type="text" class="form-control" name="title" value="<?php echo Html::encode($alert->title)?>">
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-5">
            <b>Содержание:</b>
        </div>
        <div class="col-sm-7">
            <textarea name="content" class="form-control" style="resize:vertical;min-height:100px;max-height:500px;"><?php echo Html::encode($alert->content)?></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-5">
            <b>Тип уведомления:</b>
        </div>
        <div class="col-sm-7">
            <select multiple name="alert_type[]" class="form-control">
                <?php $alert_types_list = $alert->alert_type; foreach (Yii::$app->rgk->alert_types as $type_name=>$type_handler):?>
                <option value="<?php echo Html::encode($type_name);?>"<?php if (in_array($type_name, $alert_types_list)) echo ' selected';?>><?php echo Html::encode($type_name)?></option>
                <?php endforeach ;?>
            </select>
        </div>
    </div>


    <div class="form-group">
        <div class="col-xs-12 text-center">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-save"></i> Сохранить уведомление
            </button>
        </div>
    </div>

    <script>var errInputHighlight = [];</script>
    <?php if ($alert->errors):?>
        <script>
            errInputHighlight = <?php echo json_encode(array_keys($alert->errors))?>;
        </script>
        <div class="form-group">
            <div class="col-xs-12 text-center">
                <?php foreach ($alert->errors as $err):?>
                    <?php foreach($err as $errItem):?>
                        <div class="alert alert-danger">
                            <?php echo Html::encode($errItem);?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach;?>
            </div>
        </div>
    <?php endif;?>
</form>
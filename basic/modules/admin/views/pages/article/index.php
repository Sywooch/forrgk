<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use dosamigos\ckeditor\CKEditor;

    $id = !isset($id) ? 0 : $id;
?>

<h1><?php if ($id > 0) echo 'Редактирование статьи'; else echo 'Создание статьи';?></h1>
<h3>
    <?php if ($id > 0):?>
        <?php echo Html::encode($article->title);?>
    <?php else:?>
        Новая статья
    <?php endif;?>
</h3>
<br />



<form action="<?php echo Url::toRoute('write');?>&id=<?php echo $id;?>" method="POST" class="form-horizontal">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
    <div class="form-group">
        <div class="col-sm-5">
            <b>Заголовок статьи:</b>
        </div>
        <div class="col-sm-7">
            <input type="text" class="form-control" name="title" value="<?php echo Html::encode($article->title)?>">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-5">
            <b>Содержание:</b>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php CKEditor::begin(['preset' => 'standard', 'name' => 'content', 'value' => $article->content]);?><?php CKEditor::end();?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-12 text-center">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-save"></i> Сохранить статью
            </button>
        </div>
    </div>
</form>
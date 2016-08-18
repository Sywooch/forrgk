<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Регистрация на сайте';
$this->params['breadcrumbs'][] = $this->title;
?>



<div class="row">
    <div class="col-xs-12">
        <h1 class="text-center">Регистрация в системе</h1>
    </div>
</div>
<br />
<div class="row">
    <div class="col-xs-8 col-xs-offset-2">
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="index.php?r=site/checkregister" method="POST" class="form-horizontal">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                    <div class="form-group">
                        <div class="col-sm-12">
                            <h4>Основная информация</h4>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5">
                            <b>Имя пользователя</b>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" name="username" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5">
                            <b>E-mail</b>
                        </div>
                        <div class="col-sm-7">
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <h4>Пароль</h4>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5">
                            <b>Пароль</b>
                        </div>
                        <div class="col-sm-7">
                            <input type="password" name="pass1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5">
                            <b>Пароль (Повтор)</b>
                        </div>
                        <div class="col-sm-7">
                            <input type="password" name="pass2" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12 text-center">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-user-plus"></i> Зарегистрироваться!
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="alert alert-warning text-center">
                                    <i class="fa fa-info"></i> Все поля обязательны для заполнения
                            </div>
                        </div>
                    </div>
                    <script>var errInputHighlight = [];</script>
                    <?php if ($errors):?>
                        <script>
                            errInputHighlight = <?php echo json_encode(array_keys($errors))?>;
                        </script>
                    <div class="form-group">
                        <div class="col-xs-12 text-center">
                            <?php foreach ($errors as $err):?>
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
            </div>
        </div>
    </div>
</div>



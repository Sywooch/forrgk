<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
$this->title = 'TestTask For RGKGroup';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Тестовое задание</h1>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Вход в систему
                    </div>
                    <div class="panel-body">

                        <form action="index.php?r=site/checklogin" method="POST" class="form-horizontal">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                            <div class="form-group">
                                <div class="col-sm-5">
                                    <b>Имя пользователя:</b>
                                </div>
                                <div class="col-sm-7">
                                    <input type="text" name="username" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-5">
                                    <b>Пароль:</b>
                                </div>
                                <div class="col-sm-7">
                                    <input type="password" name="password" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-sign-in"></i> Войти в систему
                                    </button><br>
                                    <a class="btn btn-link" href="index.php?r=site/register">Регистрация</a>
                                </div>
                            </div>
                            <?php if (isset ($model) && $model->errors): $errors = $model->errors;?>
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

    </div>
</div>

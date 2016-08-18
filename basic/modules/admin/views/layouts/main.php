<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<?php
use yii\web\Controller;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
?>
<script>
    var waitingDialog = waitingDialog || (function ($) {
            'use strict';
            var $dialog = $(
                '<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
                '<div class="modal-dialog modal-m">' +
                '<div class="modal-content">' +
                '<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
                '<div class="modal-body">' +
                '<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>' +
                '</div>' +
                '</div></div></div>');

            return {
                show: function (message, options) {
                    // Assigning defaults
                    if (typeof options === 'undefined') {
                        options = {};
                    }
                    if (typeof message === 'undefined') {
                        message = 'Loading';
                    }
                    var settings = $.extend({
                        dialogSize: 'm',
                        progressType: '',
                        onHide: null // This callback runs after the dialog was hidden
                    }, options);

                    // Configuring dialog
                    $dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
                    $dialog.find('.progress-bar').attr('class', 'progress-bar');
                    if (settings.progressType) {
                        $dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
                    }
                    $dialog.find('h3').text(message);
                    // Adding callbacks
                    if (typeof settings.onHide === 'function') {
                        $dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
                            settings.onHide.call($dialog);
                        });
                    }
                    // Opening dialog
                    $dialog.modal();
                },
                hide: function () {
                    $dialog.modal('hide');
                }
            };

        })(jQuery);
</script>
<?php
$r = \yii::$app->controller->id;?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-left">
                Админ-панель
            </div>
            <div class="pull-right">
                <a href="<?php echo Url::to(['/site/alerts'])?>" class="btn btn-xs btn-primary"><i class="fa fa-bell"></i> Открыть уведомления <i class="fa fa-angle-double-right"></i> </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-4">
                    <ul class="nav nav-pills nav-stacked">
                        <?php foreach(\app\modules\admin\AdminModule::getInstance()->pages as $title=>$route): ?>
                        <li<?php  if (preg_replace('~/([^/]*)$~i', '', $route) === $r) echo ' class="active"';?>>
                            <a href="<?php echo Url::to([$route]);?>">
                                <?php echo Html::encode($title)?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-sm-8">
                    <?= Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <?php echo $content;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $this->endContent(); ?>
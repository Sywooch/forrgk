<?php

namespace app\modules\admin;


use Yii;
use yii\web\ForbiddenHttpException;

/**
 * admin module definition class
 */
class AdminModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    public $pages = array();

    public $title = 'Добро пожаловать';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();


        if (!Yii::$app->user->can('adminAccess')) {
            throw new ForbiddenHttpException();
        }

        // custom initialization code goes here
        $this->layout = 'main';



        // Здесь можно указать список меню
        // load pages list
        $this->pages = array(
            'Добро пожаловать' => 'default/index',
            'Статьи' => 'pages/articles/index',
            'Пользователи' => 'pages/users/index',
            'Уведомления' => 'pages/alerts/index',
            'Уведомления по событиям' => 'pages/alertsevents/index',
            'Плагины' => 'pages/plugins/index',
        );
    }
}

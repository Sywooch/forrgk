<?php

namespace app\modules\admin\controllers\pages;


use Yii;
use app\models\User;
use app\modules\admin\models\Alerts;

class AlertsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionAlert() {

        $id = (int)Yii::$app->request->get('id');
        $id = max($id, 0);


        $alert = new \stdClass();
        $alert->name = '';
        $alert->from_user = 0;
        $alert->to_user = 0;
        $alert->title = '';
        $alert->content = '';
        $alert->alert_type = array();
        $alert->post_date = '';
        $alert->errors = array();


        if ($id > 0) {
            $alert =  Alerts::findOne($id);


            if (!$alert) {
                return 'Уведомления с таким ID не найдено...';
            }
        }


        return $this->render('alert', [
            'id' => $id,
            'alert' => $alert
        ]);
    }


    // Сохранение/Создание события
    public function actionWrite() {
        $id = (int)Yii::$app->request->get('id');

        if ($id > 0) {
            $model = Alerts::findOne($id);
            if (!$model) return 'Уведомления с таким ID не существует';
        } else {
            $model = new Alerts();
        }



        $model->attributes = Yii::$app->request->post();
        $model->prepareData();

        if (!$model->validate()) {
            return $this->render('alert', [
                'id' => $id,
                'alert' => $model
            ]);
        } else {
            $model->save(false);
        }

        return $this->redirect(['pages/alerts']);
    }

    public function actionAlerts() {
        $model = new Alerts();

        $page = (int)Yii::$app->request->post('page');
        $order = Yii::$app->request->post('order');
        $asc = Yii::$app->request->post('asc');

        return json_encode($model->getList($page, $order, $asc));
    }

    public function actionRemove() {
        $id = Yii::$app->request->post('id');

        $model = new Alerts();
        $model->remove($id);

        die('OK');
    }
}

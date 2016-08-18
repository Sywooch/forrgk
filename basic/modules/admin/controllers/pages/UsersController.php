<?php

namespace app\modules\admin\controllers\pages;

use Yii;
use app\modules\admin\models\Users;

class UsersController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUsers() {
        $model = new Users();

        $page = (int)Yii::$app->request->post('page');
        $order = Yii::$app->request->post('order');
        $asc = Yii::$app->request->post('asc');

        return json_encode($model->getList($page, $order, $asc));
    }

    public function actionAdmin() {
        $mode = (int)Yii::$app->request->post('mode');
        $id = (int)Yii::$app->request->post('id');

        if ($id == Yii::$app->user->getId()) return 'Вы не можете сами себя понизить';

        (new Users())->admin($id, $mode);

        return 'OK';
    }

    public function actionBan() {
        $mode = (int)Yii::$app->request->post('mode');
        $id = (int)Yii::$app->request->post('id');


        if ($id == Yii::$app->user->getId()) return 'Вы не можете сами себя забанить';

        (new Users())->ban($id, $mode);

        return 'OK';
    }
}

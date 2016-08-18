<?php

namespace app\modules\admin\controllers\pages;

use Yii;
use app\modules\admin\models\Articles;

class ArticlesController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionArticles() {
        $model = new Articles();

        $page = (int)Yii::$app->request->post('page');
        $order = Yii::$app->request->post('order');
        $asc = Yii::$app->request->post('asc');

        return json_encode($model->getList($page, $order, $asc));
    }

    public function actionRemove() {
        $id = Yii::$app->request->post('id');

        $model = new Articles();
        $model->remove($id);

        die('OK');
    }
}

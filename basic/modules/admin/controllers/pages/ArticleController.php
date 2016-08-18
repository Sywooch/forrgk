<?php

namespace app\modules\admin\controllers\pages;

use Yii;
use app\modules\admin\models\Articles;

class ArticleController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $id = (int)Yii::$app->request->get('id');

        $article = new \stdClass();

        $article->title = '';
        $article->content = '';
        $article->post_date = '';
        $article->author = 0;


        if ($id > 0) {
            $article =  Articles::findOne($id);


            if ($article->title === NULL) {
                return 'Статьи с таким ID не найдено...';
            }
        }

        return $this->render('index', [
            'id' => $id,
            'article' => $article
        ]);
    }

    public function actionWrite() {
        $model = new Articles();
        $id = (int)Yii::$app->request->get('id');

        $model->attributes = Yii::$app->request->post();

        $model->on('newarticle', function ($event) {
            (new \app\models\Alerts())->triggerAlert('newarticle', $event->vars);
        });

        if (!$model->validate() || !$model->write($id)) {
            return $this->render('index', [
                'id' => $id,
                'article' => $model
            ]);
        }

        return $this->redirect(['pages/articles']);
    }

}

<?php

namespace app\modules\admin\controllers\pages;


use app\modules\admin\models\Plugins;

use Yii;

class PluginsController extends \yii\web\Controller
{
    const ITEMS_PER_PAGE = 10;

    public function actionIndex()
    {
        $model = new Plugins();

        $available = $model->getAvailableList();
        $enabled   = $model->getEnabledList();


        $plugins = array();


        foreach ($available as $item) {
            $plugins[] = array(
                'name' => $item,
                'enabled' => (bool)in_array($item, $enabled)
            );
        }

        return $this->render('index',
            [
                'plugins' => $plugins
            ]);
    }

    public function actionSwitch() {
        (new Plugins())->toggleEnable(Yii::$app->request->get('name'));

        return $this->redirect(['pages/plugins']);
    }

}

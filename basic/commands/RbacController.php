<?php




namespace app\commands;

use yii;
use yii\console\Controller;

/**
 * Генератор массива разерешений для RBAC
 *
 * Создается командой: yii rbac/init
 */
class RbacController extends Controller
{

    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // доступ к админке
        $adminAccess = $auth->createPermission('adminAccess');
        $adminAccess->description = 'Доступ к админпанели';
        $auth->add($adminAccess);


        $userRole = $auth->createRole('user');
        $adminRole = $auth->createRole('admin');

        $auth->add($userRole);
        $auth->add($adminRole);

        // разрешаем админу попасть в админку

        $auth->addChild($adminRole, $adminAccess);
    }
}

<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $email
 * @property string $last_access
 * @property string $last_enter
 * @property integer $halted
 * @property string $token
 */
class Users extends \yii\db\ActiveRecord
{
    const ITEMS_PER_PAGE = 10;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'salt', 'email', 'last_access', 'last_enter', 'token'], 'required'],
            [['last_access', 'last_enter'], 'safe'],
            [['halted'], 'integer'],
            [['username'], 'string', 'max' => 32],
            [['password'], 'string', 'max' => 60],
            [['salt'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 64],
            [['token'], 'string', 'max' => 48],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'salt' => 'Salt',
            'email' => 'Email',
            'last_access' => 'Last Access',
            'last_enter' => 'Last Enter',
            'halted' => 'Halted',
            'token' => 'Token',
        ];
    }

    public function getList($page, $order, $asc='asc') {
        $arr = [];

        $count = (int)Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{users}}')->queryScalar();
        $pages = ceil($count / static::ITEMS_PER_PAGE);

        $page = max(1, min($page, $pages));

        $offset = $page * static::ITEMS_PER_PAGE - static::ITEMS_PER_PAGE;

        $q = (new \yii\db\Query())->select('*, (SELECT COUNT(*) FROM {{auth_assignment}} WHERE {{users}}.id={{auth_assignment}}.user_id AND {{auth_assignment}}.item_name=\'admin\') AS [[is_admin]]')->from('users');

        if ($order) {
            $q->orderBy([
                $order => ($asc === 'desc' ? SORT_DESC : SORT_ASC)
            ]);
        }

        $q->limit(static::ITEMS_PER_PAGE)->offset($offset);

        $result = $q->all();
        $q = NULL;

        $arr['count'] = $count;
        $arr['page'] = $page;
        $arr['pages'] = $pages;
        $arr['items'] = $result;

        return $arr;
    }

    public function remove($id) {
        Yii::$app->db->createCommand('DELETE FROM `users` WHERE id=:id')->bindValue(':id', $id)->execute();
        return true;
    }

    public function admin($id, $mode) {

        $adminRole = Yii::$app->authManager->getRole('admin');

        if ($mode == 1) {
            // set admin
            if (!Yii::$app->authManager->checkAccess($id, 'adminAccess')) {
                Yii::$app->authManager->assign($adminRole, $id);
            }
        } else {
            // revoke admin
            Yii::$app->authManager->revoke($adminRole, $id);
        }
    }

    public function ban($id, $mode) {
        $u = static::findOne($id);
        $u->halted = $mode ? 1 : 0;
        $u->save(false);
    }
}

<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;
use yii\base\Security;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    public static function tableName() {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Ищет сущность по юзернейму
     *
     * @param $username
     * @return null|static
     */
    public static function findIdentityByUsername($username) {
        $username = mb_strtolower($username);
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->token === $authKey;
    }

    public function validatePassword($password) {
        //var_dump(Yii::$app->getSecurity()->generatePasswordHash($this->salt.$this->password.$this->salt));die;
        return Yii::$app->getSecurity()->validatePassword($this->salt.$password.$this->salt, $this->password);
    }


    public static function getAll() {
        return Yii::$app->db->createCommand('SELECT * FROM {{users}} LIMIT 1000')->queryAll();
    }
}

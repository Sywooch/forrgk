<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Event;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class RegisterForm extends Model
{

    public function rules() {
        return [
            [['username', 'pass1', 'pass2', 'email'], 'required'],

            ['email', 'email'],

            ['pass1', 'verifyPassword'],
            ['username', 'verifyUsername']
        ];
    }

    public function verifyPassword($attrs, $params) {
        if (!$this->hasErrors()) {
            if (strlen($this->pass1) < 6) {
                $this->addError($attrs, 'Пароль не может состоять меньше 6 символов');
            } elseif ($this->pass1 != $this->pass2) {
                $this->addError($attrs, 'Введенные пароли не совпадают');
            } elseif ($this->pass1 == $this->username) {
                $this->addError($attrs, 'Пароль не должен совпадать с именем пользователя');
            }
        }
    }

    public function verifyUsername($attrs, $params) {

        $this->username = mb_strtolower($this->username);

        if (!$this->hasErrors()) {
            if (!preg_match('~^[a-z0-9\\._]{3,32}$~',$this->username)) {
                $this->addError($attrs, 'Неверно введено имя пользователя. Имя пользователя может состоять из латинских букв точки "." и знака подчеркивания "_", при этом длина не должна быть меньше 3 символов и небольше 2 символов.');
            }else {
                $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{users}} WHERE username=:username')
                    ->bindValue(':username', $this->username)
                    ->queryScalar();

                if ($count > 0) {
                    $this->addError($attrs, 'Пользователь с таким именем уже существует в системе');
                }
            }
        }
    }

    public function register() {
        $salt = Yii::$app->getSecurity()->generateRandomString(16);
        $passh = Yii::$app->getSecurity()->generatePasswordHash($salt.$this->pass1.$salt);
        $username = $this->username;
        $email = $this->email;
        $last_access = $last_enter = date('Y-m-d H:i:s');

        Yii::$app->db->createCommand()->insert('users', [
            'username' => $username,
            'password' => $passh,
            'salt' => $salt,
            'email' => $email,
            'last_access' => $last_access,
            'last_enter' => $last_enter
        ])->execute();


        $id = Yii::$app->db->getLastInsertID();

        $userRole = Yii::$app->authManager->getRole('user');
        Yii::$app->authManager->assign($userRole, $id);


        // call event
        $ev = new \app\classes\EventData;
        $ev->vars['newUsername'] = $username;
        $ev->vars['newUseremail'] = $email;
        $this->trigger('register', $ev);

        return true;
    }
}


?>
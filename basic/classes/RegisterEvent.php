<?php
/**
 * Created by PhpStorm.
 * User: maalik
 * Date: 18.08.16
 * Time: 0:19
 */

namespace app\classes;

use app\interfaces\EventType;

class RegisterEvent implements EventType
{
    public function handleData(&$inputData)
    {

    }

    public function varList()
    {
        return [
            'newUsername' => 'Имя зарегистрировавшегося пользователя'
        ];
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: maalik
 * Date: 18.08.16
 * Time: 0:19
 */

namespace app\classes;

use app\interfaces\EventType;

class LoginEvent implements EventType
{
    public function handleData(&$inputData)
    {

    }

    public function varList()
    {
        return [
            'loggedUsername' => 'Имя пользователя, который залогинился'
        ];
    }
}
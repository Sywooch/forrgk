<?php


namespace app\classes;

use app\interfaces\AlertType;

class BrowserAlert implements AlertType
{
    public function handleAlert($alert) {
        // Для браузерного уведомления мы тут ничего не делаем, всё и так ясно.
    }
}
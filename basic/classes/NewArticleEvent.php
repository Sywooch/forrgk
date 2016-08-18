<?php
/**
 * Created by PhpStorm.
 * User: maalik
 * Date: 18.08.16
 * Time: 0:19
 */

namespace app\classes;

use app\interfaces\EventType;

class NewArticleEvent implements EventType
{
    public function handleData(&$inputData)
    {

    }

    public function varList()
    {
        return [
            'articleName' => 'Заголовок статьи',
            'articleUrl' => 'Ссылка на статью (Абсолютная)',
            'shortText' => 'Краткий текст статьи',
            'readMore' => 'HTML-кнопка "Читать далее"'
        ];
    }
}
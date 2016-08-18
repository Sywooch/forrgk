<?php
/**
 * Created by PhpStorm.
 * User: maalik
 * Date: 18.08.16
 * Time: 2:29
 */

namespace app\classes;

use yii\base\Event;

class EventData extends Event
{
    public $vars = array();
}
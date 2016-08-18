<?php


namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;


function includefile($file,$vars=array()) {
    extract($vars, EXTR_REFS);
    include($file);
}

class Rgk extends Component
{
    public $plugins = array();
    public $alert_types = array();
    public $event_types = array();

    public function init() {

        // init Типов алертов
        $this->alert_types['browser'] = new \app\classes\BrowserAlert();


        // типы событий
        $this->event_types['register'] = new \app\classes\RegisterEvent();
        $this->event_types['login'] = new \app\classes\LoginEvent();
        $this->event_types['logout'] = new \app\classes\LogoutEvent();
        $this->event_types['newarticle'] = new \app\classes\NewArticleEvent();



        // Загружаем расширения
        $enabledPlugins = (new \app\modules\admin\models\Plugins())->getEnabledList();

        foreach ($enabledPlugins as $plg) {
            if (isset($this->plugins[$plg])) continue;
            $plg_file = dirname(__FILE__).'/../plugins/'.$plg;
            if (is_file($plg_file)) {
                includefile($plg_file, array(
                    'plugins' => &$this->plugins,
                    'alert_types' => &$this->alert_types,
                    'event_types' => &$this->event_types
                ));
            }
        }



        parent::init();
    }

}

?>
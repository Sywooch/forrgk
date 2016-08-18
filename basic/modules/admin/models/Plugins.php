<?php

/**
 * Менеджер плагинов
 */

namespace app\modules\admin\models;

use yii;



class Plugins extends yii\base\Model {

    /**
     * Получает список доступных плагинов
     */
    public function getAvailableList() {
        $plugins_dir = dirname(__FILE__).'/../../../plugins';
        $plugins = scandir($plugins_dir);
        array_shift($plugins);
        array_shift($plugins);

        $ret = array();

        foreach ($plugins as $plugin) {
            if (is_file($plugins_dir.'/'.$plugin) && preg_match('~\\.php$~i', $plugin)) {
                $ret[] = $plugin;
            }
        }

        return $ret;
    }

    public function getEnabledList() {
        return Yii::$app->db->createCommand('SELECT [[name]] FROM {{plugins}}')->queryColumn();
    }

    public function toggleEnable($name) {
        $enabled = (int)Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{plugins}} WHERE [[name]]=:name')
            ->bindValue(':name', $name)
            ->queryScalar();


        if ($enabled) {
            Yii::$app->db->createCommand()->delete('plugins', 'name = :name')->bindValue(':name', $name)->execute();
        } else {
            Yii::$app->db->createCommand()->insert('plugins', [
                'name' => $name
            ])->execute();
        }
    }
}


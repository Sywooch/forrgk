<?php



namespace app\models;


use Yii;
use app\modules\admin\models\Alerts as ModelAlerts;

class Alerts extends \yii\base\Model {
    const ITEMS_PER_PAGE = 10;

    public function getListForUser($sAlertType, $iUserId, $iPage) {
        $count = (int)Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{alerts}} WHERE (to_user=:user_id OR to_user=0) AND :alert_type IN (SELECT type_name FROM alerts_types WHERE `alerts_types`.`alert_id`=`alerts`.`id`)')
            ->bindValue(':alert_type', $sAlertType)
            ->bindValue(':user_id', $iUserId)
            ->queryScalar();


        $pages = ceil($count / static::ITEMS_PER_PAGE);
        $iPage = min($pages, max(0, $iPage));

        $offset = $iPage * static::ITEMS_PER_PAGE - static::ITEMS_PER_PAGE;


        // get data
        $q = (new \yii\db\Query())->select('var_list,id,title,from_user,(SELECT username FROM `users` WHERE `users`.`id`=`alerts`.`from_user`) AS from_username,to_user,(SELECT username FROM `users` WHERE `users`.`id`=`alerts`.`to_user`) AS to_username,content,post_date,(SELECT COUNT(*) FROM alerts_views WHERE alerts_views.alert_id=`alerts`.`id` AND `alerts_views`.`user_id`='.((int)$iUserId).') AS viewed')->from('alerts');
        $q->where('(to_user=:to_user OR to_user=0) AND :alert_type IN (SELECT type_name FROM alerts_types WHERE `alerts_types`.`alert_id`=`alerts`.`id`)', [
            ':to_user' => $iUserId,
            ':alert_type' => $sAlertType
        ]);
        $q->orderBy('post_date DESC');
        $q->offset($offset)->limit(static::ITEMS_PER_PAGE);

        $result = $q->all();

        foreach ($result as &$r) {
            $vars = json_decode($r['var_list'], true);
            if (!$vars) $vars = array();
            $r['title'] = $this->replaceVars($r['title'], $vars);
            $r['content'] = $this->replaceVars($r['content'], $vars);
            unset($r['var_list']);
        }

        $q = NULL;

        $arr['count'] = $count;
        $arr['page'] = $iPage;
        $arr['pages'] = $pages;
        $arr['items'] = $result;

        return $arr;
    }

    public function setReaded($iUserId, $iAlertId) {
        $count = (int)Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{alerts}} WHERE (to_user=0 OR to_user=:to_user) AND id=:id')
            ->bindValue(':to_user', $iUserId)
            ->bindValue(':id', $iAlertId)
            ->queryScalar();

        if (!$count) {
            return false;
        }


        Yii::$app->db->createCommand()->insert('alerts_views', [
            'alert_id' => $iAlertId,
            'user_id' => $iUserId,
            'view_date' => date('Y-m-d H:i:s')
        ])->execute();
        return true;
    }



    // обрабатывает события из алертов
    public function triggerAlert($sEventType, $vars = array()) {
        $vars['event'] = $sEventType;

        if (!in_array($sEventType, array_keys(Yii::$app->rgk->event_types))) {
            throw new \Exception('Undefined event name - '.$sEventType);
        }

        $q = Yii::$app->db->createCommand('SELECT * FROM {{alerts_events}} WHERE id IN (SELECT alert_event_id FROM {{alerts_events_assignment}} WHERE event_name=:event_name)')->bindValue(':event_name', $sEventType)->queryAll();


        foreach ($q as $alert_tpl) {

            $types = Yii::$app->db->createCommand('SELECT type_name FROM {{alerts_types}} WHERE for_event=1 AND alert_id=:alert_id')->bindValue(':alert_id', $alert_tpl['id'])->queryColumn();

            $model = new ModelAlerts();
            $model->attributes = $alert_tpl;
            $model->alert_type = $types;
            $model->var_list = json_encode($vars);


            $model->prepareData();
            $model->save(false);
            $this->handleAlert($model, $vars);
        }


        //
    }



    public function handleAlert($alertModel, $vars=array())
    {
        $alertModel->title = static::replaceVars($alertModel->title, $vars);
        $alertModel->content = static::replaceVars($alertModel->content, $vars);

        foreach ($alertModel->alert_type as $type) {
            if (isset(Yii::$app->rgk->alert_types[$type])) {
                Yii::$app->rgk->alert_types[$type]->handleAlert($alertModel);
            }
        }


    }

    public static function replaceVars($content, $vars) {
        // Задаем глобальные переменные
        $vars['username'] = Yii::$app->user->isGuest ? 'Гость' : Yii::$app->user->identity->username;
        $vars['sitename'] = 'Тестовое задание';
        if (!isset($vars['event'])) $vars['event'] = 'undefined';


        // load event type manager
        if (isset(Yii::$app->rgk->event_types[$vars['event']])) {
            Yii::$app->rgk->event_types[$vars['event']]->handleData($vars);
        }

        /// PROCESS ifevent/ifnevent
        while (preg_match('~{ifn?event=([a-z0-9_\\.]+)}~i', $content)) {
            $content = preg_replace_callback('~{if(n)?event=([a-z0-9_\\.]+)}(.*){/endif}~is', function ($m) use ($vars) {
                $inv = $m[1] != '';
                $ev = $m[2];
                $rep = $m[3];

                if ($inv) {
                    if ($ev !== $vars['event']) {
                        return $rep;
                    }
                } else {
                    if ($ev === $vars['event']) {
                        return $rep;
                    }
                }

                return '';
            }, $content);
        }


        foreach ($vars as $name=>$val)
            $content = str_ireplace('{'.$name.'}', $val, $content);


        return $content;
    }
}
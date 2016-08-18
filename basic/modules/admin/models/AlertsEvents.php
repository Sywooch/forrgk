<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "alerts_events".
 *
 * @property string $id
 * @property string $name
 * @property string $from_user
 * @property string $to_user
 * @property string $title
 * @property string $content
 * @property string $alert_type
 * @property integer $position
 */
class AlertsEvents extends \yii\db\ActiveRecord
{
    const ITEMS_PER_PAGE = 10;

    public $event_type = array();

    public $alert_type = array();
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'alerts_events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title', 'content'], 'trim'],
            [['name', 'title', 'content', 'alert_type'], 'required'],
            [['from_user', 'to_user'], 'integer'],
            [['from_user', 'to_user'], 'validateUsers'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 32],
            [['title'], 'string', 'max' => 128],
            [['content'], 'string', 'max' => 4096],
            [['alert_type'], 'validateAlertType'],
            [['event_type'], 'validateEventType']
        ];
    }

    public function prepareData() {
        $this->from_user = (int)$this->from_user;
        $this->to_user = (int)$this->to_user;
    }

    public function validateAlertType($attr, $params) {
        if (!is_array($this->alert_type) || !count($this->alert_type)) {
            $this->addError($attr, 'Не выбран тип уведомления');
        }

        foreach($this->alert_type as $a) {
            $not_type = true;
            foreach (Yii::$app->rgk->alert_types as $alert_name=>$alert_handler) {
                if ($a == $alert_name) {
                    $not_type = false;
                    break;
                }
            }

            if ($not_type) {
                $this->addError($attr, 'Неверно указан тип события');
                return;
            }
        }
    }

    public function validateEventType($attr, $params) {
        if (!is_array($this->event_type) || !count($this->event_type)) {
            $this->addError($attr, 'Не выбран тип события уведомления');
        }

        foreach($this->event_type as $a) {
            $not_type = true;
            foreach (Yii::$app->rgk->event_types as $event_name=>$event_handler) {
                if ($a == $event_name) {
                    $not_type = false;
                    break;
                }
            }

            if ($not_type) {
                $this->addError($attr, 'Неверно указан тип события');
                return;
            }
        }
    }

    public function validateUsers($attr, $params) {
        $uid = $this->$attr;

        if ($uid == 0) return;

        $c = Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{users}} WHERE id=:id')
            ->bindValue(':id', $uid)
            ->queryScalar();


        if ($c == 0) {
            $this->addError($attr, 'Пользователя с таким ID не было найдено');
        }
    }

    public function beforeSave($insert)
    {

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changed) {
        $id = Yii::$app->db->getLastInsertID();
        if (!$insert) {
            $id = $this->id;
            Yii::$app->db->createCommand()->delete('alerts_events_assignment', 'alert_event_id=' . $id)->execute();
            Yii::$app->db->createCommand()->delete('alerts_types', 'alert_id='.$id)->execute();
        }

        foreach ($this->event_type as $event) {
            Yii::$app->db->createCommand()->insert('alerts_events_assignment', [
                'alert_event_id' => $id,
                'event_name' => $event
            ])->execute();
        }

        foreach ($this->alert_type as $alert) {
            Yii::$app->db->createCommand()->insert('alerts_types', [
                'alert_id' => $id,
                'type_name' => $alert,
                'for_event' => 1
            ])->execute();
        }


    }

    public function afterFind() {
        $id = $this->id;

        $this->event_type = Yii::$app->db->createCommand('SELECT event_name FROM {{alerts_events_assignment}} WHERE alert_event_id=:evid')
            ->bindValue(':evid', $id)
            ->queryColumn();
        if (!$this->event_type) $this->event_type = array();



        $this->alert_type = Yii::$app->db->createCommand('SELECT type_name FROM {{alerts_types}} WHERE alert_id=:aid AND for_event=1')
            ->bindValue(':aid', $id)
            ->queryColumn();

        if (!$this->alert_type) $this->alert_type = array();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'from_user' => 'From User',
            'to_user' => 'To User',
            'title' => 'Title',
            'content' => 'Content',
            'alert_type' => 'Alert Type',
            'position' => 'Position',
        ];
    }

    public function getList($page, $order, $asc='asc') {
        $arr = [];

        $count = (int)Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{alerts_events}}')->queryScalar();
        $pages = ceil($count / static::ITEMS_PER_PAGE);

        $page = max(1, min($page, $pages));

        $offset = $page * static::ITEMS_PER_PAGE - static::ITEMS_PER_PAGE;

        $q = (new \yii\db\Query())->select('id,name,(SELECT GROUP_CONCAT(type_name SEPARATOR \',\') FROM alerts_types WHERE `alerts_types`.`alert_id`=`alerts_events`.`id` AND `alerts_types`.`for_event`=1 GROUP BY `alert_id`) AS alert_type')->from('alerts_events');

        if ($order) {
            $q->orderBy([
                $order => ($asc === 'desc' ? SORT_DESC : SORT_ASC)
            ]);
        }

        $q->limit(static::ITEMS_PER_PAGE)->offset($offset);

        $result = $q->all();
        $q = NULL;

        $arr['count'] = $count;
        $arr['page'] = $page;
        $arr['pages'] = $pages;
        $arr['items'] = $result;
        $arr['order'] = $order;
        $arr['asc'] = $asc;

        return $arr;
    }

    public function remove($id) {
        Yii::$app->db->createCommand('DELETE FROM `alerts_events` WHERE id=:id')->bindValue(':id', $id)->execute();
        return true;
    }
}

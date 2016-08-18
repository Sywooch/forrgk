<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "alerts".
 *
 * @property string $id
 * @property string $name
 * @property integer $from_user
 * @property integer $to_user
 * @property string $title
 * @property string $content
 * @property string $alert_type
 * @property string $post_date
 * @property string $varlist
 */
class Alerts extends \yii\db\ActiveRecord
{
    const ITEMS_PER_PAGE = 10;

    public $alert_type = array();

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'alerts';
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
            [['content', 'var_list'], 'string'],
            [['name'], 'string', 'max' => 32],
            [['title'], 'string', 'max' => 128],
            [['content'], 'string', 'max' => 4096],
            [['alert_type'], 'validateAlertType'],
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

        $this->post_date = date('Y-m-d H:i:s');

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $id = Yii::$app->db->getLastInsertID();

        if (!$insert) {
            $id = $this->id;
            Yii::$app->db->createCommand()->delete('alerts_types', 'alert_id='.$id)->execute();
        }

        foreach ($this->alert_type as $alert) {
            Yii::$app->db->createCommand()->insert('alerts_types', [
                'alert_id' => $id,
                'type_name' => $alert,
                'for_event' => 0
            ])->execute();
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind() {
        $id = $this->id;

        $this->alert_type = Yii::$app->db->createCommand('SELECT type_name FROM {{alerts_types}} WHERE alert_id=:aid AND for_event=0')
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
            'post_date' => 'Post Date',
            'var_list' => 'Variables list'
        ];
    }

    public function getList($page, $order, $asc='asc') {
        $arr = [];

        $count = (int)Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{alerts}}')->queryScalar();
        $pages = ceil($count / static::ITEMS_PER_PAGE);

        $page = max(1, min($page, $pages));

        $offset = $page * static::ITEMS_PER_PAGE - static::ITEMS_PER_PAGE;

        $q = (new \yii\db\Query())->select('id,name,(SELECT GROUP_CONCAT(type_name SEPARATOR \',\') FROM alerts_types WHERE `alerts_types`.`alert_id`=`alerts`.`id` AND `alerts_types`.`for_event`=0 GROUP BY `alert_id`) AS alert_type,post_date')->from('alerts');

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

        return $arr;
    }

    public function remove($id) {
        Yii::$app->db->createCommand('DELETE FROM `alerts` WHERE id=:id')->bindValue(':id', $id)->execute();
        return true;
    }
}

<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * This is the model class for table "articles".
 *
 * @property string $id
 * @property string $title
 * @property string $content
 * @property string $post_date
 * @property string $author
 */
class Articles extends \yii\db\ActiveRecord
{
    const ITEMS_PER_PAGE = 10;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'articles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['post_date'], 'safe'],
            [['author'], 'integer'],
            [['title'], 'string', 'max' => 64],
            [['content'], 'string', 'max' => 4096],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'post_date' => 'Post Date',
            'author' => 'Author',
        ];
    }

    public function write($id=0) {

        if (!$this->validate()) return false;


        $title = $this->title;
        $content = $this->content;

        $a = $this;
        if ($id > 0) {
            $a = static::findOne($id);
            if (!$a) {
                $this->addError('id', 'Статья с таким ID не найдена');
                return false;
            }
        }


        $a->title = trim($title);
        $a->content = trim($content);
        $a->post_date = date('Y-m-d H:i:s');
        $a->author = Yii::$app->user->getId();

        $a->save();
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {

        if ($insert) {
            $ev = new \app\classes\EventData;
            $ev->vars['articleName'] = $this->title;
            $ev->vars['articleUrl'] = Url::to(['/site/article']).'&id='.$this->id;
            $ev->vars['shortText'] = mb_substr($this->content,0,100).'...';
            $ev->vars['readMore'] = '<a href="'.Html::encode($ev->vars['articleUrl']).'" class="btn btn-link">Читать далее</a>';
            $this->trigger('newarticle', $ev);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function getList($page, $order, $asc='asc') {
        $arr = [];

        $count = (int)Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{articles}}')->queryScalar();
        $pages = ceil($count / static::ITEMS_PER_PAGE);

        $page = max(1, min($page, $pages));

        $offset = $page * static::ITEMS_PER_PAGE - static::ITEMS_PER_PAGE;

        $q = (new \yii\db\Query())->select('*, (SELECT username FROM {{users}} WHERE {{users}}.id={{articles}}.author) AS [[author_name]]')->from('articles');

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
        Yii::$app->db->createCommand('DELETE FROM `articles` WHERE id=:id')->bindValue(':id', $id)->execute();
        return true;
    }
}

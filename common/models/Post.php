<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property integer $visible
 * @property integer $view_count
 * @property integer $help_count
 * @property integer $useless_count
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Category $category
 * @property User $user
 * @property Step[] $steps
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord)$this->user_id = Yii::$app->user->identity->id;
        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {   /* Удаляем все шаги в инструкции и картинки*/
        foreach (Step::find()->where(['post_id' => $this->id])->all() as $step) {
            $step->delete();
        }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'user_id', 'visible', 'view_count', 'help_count', 'useless_count', 'created_at', 'updated_at'], 'integer'],
            [['title', 'description'], 'required'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Категория',
            'user_id' => 'Автор',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'visible' => 'На сайте',
            'view_count' => 'Просмотры',
            'help_count' => 'Помогла',
            'useless_count' => 'Бесполезна',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата редактирования',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSteps()
    {
        return $this->hasMany(Step::className(), ['post_id' => 'id']);
    }
}

<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "studenthandgoals".
 *
 * @property int $id
 * @property int $user_id Students
 * @property int $category_id Kategory
 *
 * @property Handdifficulties $category
 * @property Users $user
 */
class Studenthandgoals extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studenthandgoals';
    }

    function rules()
    {
        return [
            [['user_id', 'category_id'], 'required'],
            [['user_id', 'category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Handdifficulties::class, 'targetAttribute' => ['category_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app',  'Student'),
            'category_id' => \Yii::t('app',  'Category'),
        ];
    }

    function getCategory()
    {
        return $this->hasOne(Handdifficulties::class, ['id' => 'category_id']);
    }

    function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    public static function removeUserGoals($id)
    {
        return self::deleteAll(['user_id' => $id]);
    }

    public static function getUserGoals($id)
    {
        return ArrayHelper::map(self::find()->where(['user_id' => $id])->asArray()->all(), 'category_id', 'id');
    }
}

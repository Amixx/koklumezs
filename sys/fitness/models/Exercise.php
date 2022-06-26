<?php

namespace app\fitness\models;

use app\models\Users;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Exercise extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_exercises';
    }

    public function rules()
    {
        return [
            [['author_id', 'name'], 'required'],
            [['author_id'], 'integer'],
            [['name', 'first_set_video', 'other_sets_video', 'technique_video'], 'string'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app',  'Author ID'),
            'name' => \Yii::t('app',  'Title'),
            'first_set_video' => \Yii::t('app', 'Video for first set'),
            'other_sets_video' => \Yii::t('app', 'Video for other sets'),
            'technique_video' => \Yii::t('app', 'Technique video'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ]
        ];
    }


    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }
}

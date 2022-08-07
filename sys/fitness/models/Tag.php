<?php

namespace app\fitness\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Users;

class Tag extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_tags';
    }

    public function rules()
    {
        return [
            [['author_id', 'value'], 'required'],
            [['author_id'], 'integer'],
            [['value', 'description'], 'string'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app',  'Author ID'),
            'value' => \Yii::t('app',  'Value'),
            'description' => \Yii::t('app', 'Description'),
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

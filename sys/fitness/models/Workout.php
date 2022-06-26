<?php

namespace app\fitness\models;

use app\models\Users;

class Workout extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_workouts';
    }

    public function rules()
    {
        return [
            [['author_id', 'student_id'], 'required'],
            [['author_id', 'student_id'], 'integer'],
            [['name', 'description'], 'string'],
            [['created_at', 'opened_at'], 'safe'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app',  'Author ID'),
            'student_id' => \Yii::t('app',  'Student ID'),
            'name' => \Yii::t('app',  'Title'),
            'description' => \Yii::t('app', 'Description'),
            'created_at' => \Yii::t('app',  'Created at'),
            'opened_at' => \Yii::t('app',  'Opened at'),
        ];
    }


    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Users::class, ['id' => 'student_id']);
    }
}

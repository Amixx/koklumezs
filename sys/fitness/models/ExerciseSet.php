<?php

namespace app\fitness\models;

use app\models\Users;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class ExerciseSet extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_exercisesets';
    }

    public function rules()
    {
        return [
            [['author_id', 'exercise_id', 'video'], 'required'],
            [['author_id', 'exercise_id', 'reps', 'time_seconds'], 'integer'],
            [['video'], 'string'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app',  'Author ID'),
            'exercise_id' => \Yii::t('app',  'Exercise ID'),
            'reps' => \Yii::t('app',  'Repetitions'),
            'time_seconds' => \Yii::t('app',  'Time (seconds)'),
            'video' => \Yii::t('app', 'Video'),
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

    public function getExercise()
    {
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id']);
    }
}

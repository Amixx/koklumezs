<?php

namespace app\fitness\models;

use app\models\Users;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class InterchangeableExercise extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_interchangeable_exercises';
    }

    public function rules()
    {
        return [
            [['exercise_id_1', 'exercise_id_2'], 'required'],
            [['exercise_id_1', 'exercise_id_2'], 'integer'],
            [['exercise_id_1'], 'exist', 'skipOnError' => true, 'targetClass' => Exercise::class, 'targetAttribute' => ['exercise_id_1' => 'id']],
            [['exercise_id_2'], 'exist', 'skipOnError' => true, 'targetClass' => Exercise::class, 'targetAttribute' => ['exercise_id_2' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exercise_id_1' => \Yii::t('app',  'First exercise ID'),
            'exercise_id_2' => \Yii::t('app',  'Second exercise ID'),
        ];
    }

    public function getExercise1()
    {
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id_1']);
    }

    public function getExercise2()
    {
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id_2']);
    }
}

<?php

namespace app\fitness\models;

use app\models\Users;

class ExerciseTag extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_exercisetags';
    }

    public function rules()
    {
        return [
            [['tag_id', 'exercise_id'], 'required'],
            [['tag_id', 'exercise_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exercise_id' => \Yii::t('app',  'Exercise ID'),
            'tag_id' => \Yii::t('app',  'Tag ID'),
        ];
    }


    public function getExercise()
    {
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id']);
    }

    public function getTag()
    {
        return $this->hasOne(Tag::class, ['id' => 'tag_id']);
    }
}

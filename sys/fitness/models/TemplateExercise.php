<?php

namespace app\fitness\models;

use app\models\Users;

class TemplateExercise extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_templateexercises';
    }

    public function rules()
    {
        return [
            [['template_id', 'exercise_id'], 'required'],
            [['template_id', 'exercise_id'], 'integer'],
            [
                ['weight'], 'number',
                'numberPattern' => '/^\d+(.\d{1,2})?$/'
            ],
            [['reps', 'time_seconds'], 'integer'],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::class, 'targetAttribute' => ['template_id' => 'id']],
            [['exercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exercise::class, 'targetAttribute' => ['exercise_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => \Yii::t('app',  'Template ID'),
            'exercise_id' => \Yii::t('app',  'Exercise ID'),
            'weight' => \Yii::t('app', 'Weight'),
            'reps' => \Yii::t('app', 'Repetitions'),
            'time_seconds' => \Yii::t('app', 'Time (seconds)'),
        ];
    }


    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::class, ['id' => 'template_id']);
    }

    public function getExercise()
    {
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id'])->joinWith('videos');
    }
}

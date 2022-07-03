<?php

namespace app\fitness\models;

use app\models\Users;

class TemplateExerciseSet extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_templateexercisesets';
    }

    public function rules()
    {
        return [
            [['template_id', 'exerciseset_id'], 'required'],
            [['template_id', 'exerciseset_id'], 'integer'],
            [
                ['weight'], 'number',
                'numberPattern' => '/^\d+(.\d{1,2})?$/'
            ],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::class, 'targetAttribute' => ['template_id' => 'id']],
            [['exerciseset_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExerciseSet::class, 'targetAttribute' => ['exerciseset_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => \Yii::t('app',  'Template ID'),
            'exerciseset_id' => \Yii::t('app',  'Exercise ID'),
            'weight' => \Yii::t('app', 'Weight'),
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

    public function getExerciseSet()
    {
        return $this->hasOne(ExerciseSet::class, ['id' => 'exerciseset_id'])->joinWith('exercise');
    }
}

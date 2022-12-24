<?php

namespace app\fitness\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Users;

class ProgressionChainExercise extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_progression_chain_exercises';
    }

    public function rules()
    {
        return [
            [['progression_chain_id'], 'required'],
            [['progression_chain_id', 'exercise_id', 'difficulty_increase_percent'], 'integer'],
            [['progression_chain_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgressionChain::class, 'targetAttribute' => ['progression_chain_id' => 'id']],
            [['exercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exercise::class, 'targetAttribute' => ['exercise_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'progression_chain_id' => \Yii::t('app',  'Progression chain ID'),
            'exercise_id' => \Yii::t('app',  'Exercise ID'),
            'difficulty_increase_percent' => \Yii::t('app',  'Difficulty increase percent'),
        ];
    }

    public function getProgressionChain()
    {
        return $this->hasOne(ProgressionChain::class, ['id' => 'progression_chain_id']);
    }

    public function getExercise()
    {
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id']);
    }

    public function getMainExercise()
    {
        return $this->hasOne(ProgressionChainMainExercise::class, ['progression_chain_exercise_id' => 'id']);
    }
}

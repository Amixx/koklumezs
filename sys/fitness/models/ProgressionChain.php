<?php

namespace app\fitness\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Users;
use yii\helpers\ArrayHelper;

class ProgressionChain extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_progression_chains';
    }

    public function rules()
    {
        return [
            [['title'], 'string'],
            [['author_id'], 'required'],
            [['author_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app', 'Author ID'),
            'title' => \Yii::t('app', 'Title'),
        ];
    }

    public function getExercises()
    {
        return $this->hasMany(ProgressionChainExercise::class, ['progression_chain_id' => 'id']);
    }

    public function getMainExercise()
    {
        $chainExerciseIds = ArrayHelper::getColumn($this->exercises, 'id');
        $mainExercise = ProgressionChainMainExercise::find()->where([
            'in',
            'progression_chain_exercise_id',
            $chainExerciseIds
        ])->joinWith('weightExercise')->one();
        return $mainExercise;
    }
}

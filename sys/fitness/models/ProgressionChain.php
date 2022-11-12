<?php

namespace app\fitness\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Users;

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
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => \Yii::t('app',  'Title'),
        ];
    }

    public function getExercises()
    {
        return $this->hasMany(ProgressionChainExercise::class, ['progression_chain_id' => 'id']);
    }
}

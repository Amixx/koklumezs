<?php

namespace app\fitness\models;

use Yii;

class WeightExerciseAbilityRatio extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_weight_exercise_ability_ratio';
    }

    public function rules()
    {
        return [
            [['exercise_id_1', 'exercise_id_2', 'ratio_percent'], 'required'],
            [['exercise_id_1', 'exercise_id_2', 'ratio_percent'], 'integer'],
        ];
    }

    public function attributeLabels(){
        return [
            'exercise_id_1' => Yii::t('app', 'Exercise 1'),
            'exercise_id_2' => Yii::t('app', 'Exercise 2'),
            'ratio_percent' => Yii::t('app', 'Ratio percent ({exercise 1 weight} / {exercise 2 weight} * 100)'),
        ];
    }

    public function getExercise1(){
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id_1']);
    }

    public function getExercise2(){
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id_2']);
    }

    public function ratioPercentAsFraction(){
        return $this->ratio_percent / 100;
    }

    public static function getAbilityRatioFor($exerciseId1, $exerciseId2) {
        $normal = self::findOne([
            'exercise_id_2' => $exerciseId1,
            'exercise_id_1' => $exerciseId2,
        ]);
        if($normal) return $normal->ratioPercentAsFraction();
        $flipped = self::findOne([
            'exercise_id_1' => $exerciseId1,
            'exercise_id_2' => $exerciseId2,
        ]);
        if($flipped) return 1/$flipped->ratioPercentAsFraction();
        return null;
    }
}
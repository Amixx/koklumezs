<?php

namespace app\fitness\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Users;

class ProgressionChainMainExercise extends \yii\db\ActiveRecord
{
    public $exerciseId;

    public static function tableName()
    {
        return 'progression_chain_main_exercises';
    }

    public function rules()
    {
        return [
            [['progression_chain_exercise_id', 'weight_exercise_id', 'rep_bw_ratio_percent', 'exerciseId'], 'required'],
            [['progression_chain_exercise_id', 'weight_exercise_id', 'rep_bw_ratio_percent', 'exerciseId'], 'integer'],
            [['progression_chain_exercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgressionChainExercise::class, 'targetAttribute' => ['progression_chain_exercise_id' => 'id']],
            [['weight_exercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exercise::class, 'targetAttribute' => ['weight_exercise_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exerciseId' =>  Yii::t('app',  'Exercise'),
            'progression_chain_exercise_id' => Yii::t('app',  'Progression chain exercise'),
            'weight_exercise_id' => Yii::t('app',  'Weight exercise'),
            'rep_bw_ratio_percent' => Yii::t('app',  'Coefficient'),
        ];
    }

    public function getProgressionChainExercise()
    {
        return $this->hasOne(ProgressionChainExercise::class, ['id' => 'progression_chain_exercise_id']);
    }
}

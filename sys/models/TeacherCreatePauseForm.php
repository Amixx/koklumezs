<?php

namespace app\models;

use Yii;
use yii\base\Model;

class TeacherCreatePauseForm extends Model
{
    public $plan_id;
    public $weeks;
    public $start_date;

    public function rules()
    {
        return [
            [['weeks', 'plan_id'], 'number'],
            [['weeks', 'plan_id', 'start_date'], 'required'],
            [['start_date'], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'plan_id' => Yii::t('app',  'Plan'),
            'weeks' => Yii::t('app', 'Weeks'),
            'start_date' => \Yii::t('app',  'Start date'),
        ];
    }
}

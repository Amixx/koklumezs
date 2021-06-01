<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RegisterPaymentForm extends Model
{
    public $plan_id;
    public $paid_months_count;
    public $paid_date;

    public function rules()
    {
        return [
            [['paid_months_count', 'plan_id'], 'number'],
            [['paid_months_count', 'plan_id', 'paid_date'], 'required'],
            [['paid_date'], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'plan_id' => Yii::t('app',  'Plan'),
            'paid_months_count' => Yii::t('app', 'Paid months'),
            'paid_date' => \Yii::t('app',  'Date of payment:'),
        ];
    }
}

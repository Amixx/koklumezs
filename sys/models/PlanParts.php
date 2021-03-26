<?php

namespace app\models;

use yii\helpers\ArrayHelper;

class PlanParts extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'planparts';
    }

    public function rules()
    {
        return [
            [['school_id', 'title', 'monthly_cost'], 'required'],
            [['title'], 'string'],
            [['school_id'], 'number'],
            [['monthly_cost'], 'double'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'title' => \Yii::t('app',  'Title'),
            'monthly_cost' => \Yii::t('app',  'Monthly cost'),
        ];
    }

    public static function getPriceWithoutPvn($monthlyCost, $pvnPercent, $months = 1)
    {
        $totalCost = $monthlyCost * $months;
        $divider = 1 + ($pvnPercent / 100);
        return number_format($totalCost / $divider, 2);
    }

    public static function getPvnAmount($monthlyCost, $pvnPercent, $months = 1)
    {
        $totalCost = $monthlyCost * $months;
        return number_format($totalCost - self::getPriceWithoutPvn($totalCost, $pvnPercent), 2);
    }

    public static function getPayAmount($monthlyCost, $months = 1)
    {
        $totalCost = $monthlyCost * $months;
        return number_format($totalCost, 2);
    }

    public static function getForSchool($schoolId)
    {
        return ArrayHelper::map(self::find()->where(['school_id' => $schoolId])->asArray()->all(), 'id', 'title');
    }
}

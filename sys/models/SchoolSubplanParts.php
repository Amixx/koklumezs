<?php

namespace app\models;

class SchoolSubplanParts extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolsubplanparts';
    }

    public function rules()
    {
        return [
            [['schoolsubplan_id'], 'required'],
            [['schoolsubplan_id', 'planpart_id'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolsubplan_id' => \Yii::t('app',  'School subscription plan ID'),
            'planpart_id' => \Yii::t('app',  'Plan part ID'),
        ];
    }

    public function getPlanpart()
    {
        return $this->hasOne(PlanParts::className(), ['id' => 'planpart_id']);
    }

    public static function getForSchoolSubplan($schoolSubplanId)
    {
        return self::find()->where(['schoolsubplan_id' => $schoolSubplanId])->joinWith('planpart');
    }

    public static function getPartsForSubplan($schoolSubplanId)
    {
        $data = self::getForSchoolSubplan($schoolSubplanId)->asArray()->all();

        return array_map(function ($d) {
            return $d['planpart'];
        }, $data);
    }

    public static function getPlanTotalCost($schoolSubplanId)
    {
        $plans = self::getForSchoolSubplan($schoolSubplanId)->asArray()->all();
        $totalCost = 0;

        foreach ($plans as $plan) {
            $totalCost += $plan['planpart']['monthly_cost'];
        }

        return $totalCost;
    }
}

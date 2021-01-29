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

    public function getForSchool($schoolId)
    {
        return ArrayHelper::map(self::find()->where(['school_id' => $schoolId])->asArray()->all(), 'id', 'title');
    }
}

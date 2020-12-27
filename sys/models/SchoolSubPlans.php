<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

class SchoolSubPlans extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolsubplans';
    }

    public function rules()
    {
        return [
            [['school_id', 'name', 'monthly_cost', 'months'], 'required'],
            [['monthly_cost'], 'double'],
            [['school_id', 'months', 'max_pause_weeks', 'pvn_percent'], 'number'],
            [['name', 'description', 'files', 'message'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'name' => \Yii::t('app',  'Title'),
            'description' => \Yii::t('app',  'Description'),
            'pvn_percent' => \Yii::t('app',  'PVN (percentage)'),
            'monthly_cost' => \Yii::t('app',  'Monthly cost (with PVN)'),
            'months' => \Yii::t('app',  'Months (0 - unlimited)'),
            'max_pause_weeks' => \Yii::t('app',  'Pause weeks'),
            'files' => \Yii::t('app',  'Files'),
            'message' => \Yii::t('app',  'Message to send with the invoice'),
        ];
    }

    public function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId]);
    }

    public function getForCurrentSchool()
    {
        $schoolId = School::getCurrentSchoolId();
        return self::getForSchool($schoolId);
    }

    public function getMappedForSelection()
    {
        return ArrayHelper::map(self::getForCurrentSchool()->asArray()->all(), 'id', 'name');
    }

    public function getPrices()
    {
        $isAdmin = Yii::$app->user->identity->user_level == 'Admin';
        if(!$isAdmin){
            return ArrayHelper::map(self::getForCurrentSchool()->asArray()->all(), 'id', 'monthly_cost');
        }else{
            return ArrayHelper::map(self::find()->asArray()->all(), 'id', 'monthly_cost');
        }
    }
}

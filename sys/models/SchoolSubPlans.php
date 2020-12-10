<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

class SchoolSubPlans extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolsubplans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['school_id', 'name', 'monthly_cost', 'months'], 'required'],
            [['monthly_cost'], 'double'],
            [['school_id', 'months', 'max_pause_weeks'], 'number'],
            [['name', 'description', 'files'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'name' => \Yii::t('app',  'Title'),
            'description' => \Yii::t('app',  'Description'),
            'monthly_cost' => \Yii::t('app',  'Monthly cost'),
            'months' => \Yii::t('app',  'Months (0 - unlimited)'),
            'max_pause_weeks' => \Yii::t('app',  'Pause weeks'),
            'files' => \Yii::t('app',  'Files'),
        ];
    }

    public function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId]);
    }

    public function getForCurrentSchool()
    {
        $schoolId = SchoolTeacher::getCurrentSchoolId();
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
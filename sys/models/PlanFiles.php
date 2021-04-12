<?php

namespace app\models;

class PlanFiles extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'planfiles';
    }

    public function rules()
    {
        return [
            [['file', 'plan_id', 'title'], 'required'],
            [['file', 'title'], 'string'],
            [['plan_id'], 'integer'],
            [['plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolSubPlans::class, 'targetAttribute' => ['plan_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => \Yii::t('app',  'Title'),
            'file' => \Yii::t('app',  'File'),
            'plan_id' => \Yii::t('app',  'Plan'),
        ];
    }

    public function getPlan()
    {
        return $this->hasOne(SchoolSubPlans::class, ['id' => 'plan_id']);
    }

    public static function getFilesForPlan($planId)
    {
        return self::find()->where(['plan_id' => $planId]);
    }
}

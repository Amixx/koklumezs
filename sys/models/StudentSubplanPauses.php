<?php

namespace app\models;

class StudentSubplanPauses extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'studentsubplanpauses';
    }

    public function rules()
    {
        return [
            [['studentsubplan_id', 'weeks'], 'required'],
            [['start_date'], 'string'],
            [['studentsubplan_id', 'weeks'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'studentsubplan_id' => \Yii::t('app',  'Student subscription plan ID'),
            'weeks' => \Yii::t('app',  'Weeks'),
            'start_date' => \Yii::t('app',  'Start date'),
        ];
    }

    public function getStudentPlan()
    {
        return $this->hasOne(StudentSubPlans::className(), ['id' => 'studentsubplan_id']);
    }

    public function getForStudent($studentId)
    {
        $planId = StudentSubPlans::findOne(['user_id' => $studentId])->id;
        return self::find()->where(['studentsubplan_id' => $planId])->joinWith('studentPlan');
    }

    public function getMostRecentPauseForStudent($studentId){
        $planId = StudentSubPlans::findOne(['user_id' => $studentId])->id;
        return self::find()->where(['studentsubplan_id' => $planId])->orderBy(['start_date' => SORT_DESC])->asArray()->all()[0];
    }

    public function studentHasAnyPauses($studentId)
    {
        $planId = StudentSubPlans::findOne(['user_id' => $studentId])['id'];
        return self::find()->where(['studentsubplan_id' => $planId])->count() > 0;
    }
}
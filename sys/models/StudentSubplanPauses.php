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
        return $this->hasOne(StudentSubPlans::className(), ['id' => 'studentsubplan_id'])->joinWith('plan')->joinWith('user');
    }

    public function getForStudent($studentId)
    {
        $subplan = StudentSubPlans::findOne(['user_id' => $studentId]);
        if($subplan == null) return null;

        return self::find()->where(['studentsubplan_id' => $subplan['id']])->joinWith('studentPlan');
    }

    public static function getForSchool($schoolId) {
        return self::find()->joinWith('studentPlan')->where(['schoolsubplans.school_id' => $schoolId]);
    }

    public function getMostRecentPauseForStudent($studentId){
        $subplan = StudentSubPlans::findOne(['user_id' => $studentId]);
        if($subplan == null) return null;

        return self::find()->where(['studentsubplan_id' => $subplan['id']])->orderBy(['start_date' => SORT_DESC])->asArray()->all()[0];
    }

    public function studentHasAnyPauses($studentId)
    {
        $subplan = StudentSubPlans::findOne(['user_id' => $studentId]);
        if($subplan == null) return null;

        return self::find()->where(['studentsubplan_id' => $subplan['id']])->count() > 0;
    }

    public static function getForCurrentSchool(){
        $schoolId = School::getCurrentSchoolId();
        return self::getForSchool($schoolId);
    }
}

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

    public static function getForStudent($studentId)
    {
        $subplan = StudentSubPlans::getCurrentForStudent($studentId);
        if($subplan == null) return null;

        return self::getForStudentSubplan($subplan['id']);
    }

    public static function getForStudentSubplan($subplanId){
        return self::find()->where(['studentsubplan_id' => $subplanId])->joinWith('studentPlan');       
    }

    public static function getForSchool($schoolId) {
        return self::find()->joinWith('studentPlan')->where(['schoolsubplans.school_id' => $schoolId]);
    }

    public static function getMostRecentPauseForStudent($studentId){
        $subplan = StudentSubPlans::getCurrentForStudent($studentId);
        if($subplan == null) return null;

        return self::find()->where(['studentsubplan_id' => $subplan['id']])->orderBy(['start_date' => SORT_DESC])->asArray()->all()[0];
    }

    public static function studentHasAnyPauses($studentId)
    {
        $subplan = StudentSubPlans::getCurrentForStudent($studentId);
        if($subplan == null) return null;

        return self::find()->where(['studentsubplan_id' => $subplan['id']])->count() > 0;
    }

    public static function getForCurrentSchool(){
        $schoolId = School::getCurrentSchoolId();
        return self::getForSchool($schoolId);
    }

    public static function isStudentCurrentlyPaused($studentId){
        $studentPauses = self::getForStudent($studentId);
        if($studentPauses == null) return false;
        $res = false;

        date_default_timezone_set('EET');
        foreach($studentPauses->asArray()->all() as $pause){
            $weeks = $pause['weeks'];
            if($weeks == 0) continue;

            $date = date('Y-m-d H:m:s', strtotime("-$weeks week"));
            if($pause['start_date'] > $date) $res = true;
        }

        return $res;
    }
}

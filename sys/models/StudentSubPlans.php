<?php

namespace app\models;

class StudentSubPlans extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'studentsubplans';
    }

    public function rules()
    {
        return [
            [['user_id', 'plan_id', 'start_date'], 'required'],
            [['start_date'], 'string'],
            [['user_id', 'plan_id', 'sent_invoices_count', 'times_paid'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app',  'Student ID'),
            'plan_id' => \Yii::t('app',  'Subscription plan ID'),
            'start_date' => \Yii::t('app',  'Plan start date'),
            'sent_invoices_count' => \Yii::t('app',  'Sent invoices count'),
            'times_paid' => \Yii::t('app',  'Times paid'),
        ];
    }

    public function getPlan()
    {
        return $this->hasOne(SchoolSubPlans::className(), ['id' => 'plan_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function getForStudent($studentId)
    {
        return self::find()->where(['user_id' => $studentId])->joinWith('plan')->one();
    }

    public function getForStudentEdit($studentId)
    {
        return self::find()->where(['user_id' => $studentId]);
    }

    public function getPlanEndDatesForCurrentSchoolStudents(){
        $isAdmin = \Yii::$app->user->identity->user_level == 'Admin';
        if($isAdmin) return [];

        $schoolId = School::getCurrentSchoolId();
        $studentPlans = self::find()->joinWith("plan")->andFilterWhere(['schoolsubplans.school_id' => $schoolId])->asArray()->all();
        $planEndDates = array_map(function ($studentPlan) {
            $planPauses = StudentSubplanPauses::getForStudent($studentPlan['user_id'])->asArray()->all();
            $date = date_create($studentPlan["start_date"]);
            $date->modify("+" . $studentPlan['plan']['months'] . "month");
            foreach($planPauses as $pause){
                $date->modify("+" . $pause['weeks'] . "week");
            }
            return date_format($date, 'd-m-Y');
        }, $studentPlans);
        return $planEndDates;
    }

    public function getReadablePlanEndDates(){
        $endDates = self::getPlanEndDatesForCurrentSchoolStudents();
        $endDatesMapped = array_map(function($date){
            $timestamp = strtotime($date);
            return date("M", $timestamp) . " " . date("Y", $timestamp);
        }, $endDates);
        return array_combine($endDates, $endDatesMapped);
    }

    public function getRemainingPauseWeeks($studentId){
        $pauses = StudentSubplanPauses::getForStudent($studentId)->asArray()->all();
        $subplan = self::getForStudent($studentId);
        $totalPausedWeeks = 0;
        foreach($pauses as $p){
            $totalPausedWeeks += $p['weeks'];
        }

        return $subplan->plan->max_pause_weeks - $totalPausedWeeks;
    }

    public function isPlanCurrentlyPaused($studentId){
        if(!StudentSubplanPauses::studentHasAnyPauses($studentId)) return false;

        $mostRecentPause = StudentSubplanPauses::getMostRecentPauseForStudent($studentId);
        $planEndDate = strtotime("+" . $mostRecentPause['weeks'] . " weeks", strtotime($mostRecentPause['start_date']));
        $time = time();
        return $planEndDate > $time;
    }

    public static function getForCurrentSchool(){
        $schoolId = School::getCurrentSchoolId();
        return self::find()->joinWith('plan')->where(['school_id' => $schoolId])->asArray()->all();
    }

    public function getEndDate($studentId){
        $subplan = self::getForStudent($studentId);
        if ($subplan == null) return null;
        $planPauses = StudentSubplanPauses::getForStudent($subplan['user_id'])->asArray()->all();
        $date = date_create($subplan["start_date"]);
        $date->modify("+" . $subplan['plan']['months'] . "month");
        foreach($planPauses as $pause){
            $date->modify("+" . $pause['weeks'] . "week");
        }
        $date = date_format($date, 'Y-m-d');
        $today = date('Y-m-d');
        $warningDate = date('Y-m-d', strtotime($date. ' -7 days'));

        if ($warningDate <= $today) return "<span style='background: red;'>".$date."</span>";
        else return "<span>".$date."</span>";
    }
}

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
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app',  'Student ID'),
            'plan_id' => \Yii::t('app',  'Subscription plan ID'),
            'is_active' => \Yii::t('app',  'Is active'),
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

    public function getCurrentForStudent($studentId)
    {
        return self::find()->where(['user_id' => $studentId, 'is_active' => true])->orderBy(['studentsubplans.id' => SORT_DESC])->joinWith('plan')->one();
    }

    public function getPlanEndDatesForCurrentSchoolStudents(){
        $isAdmin = \Yii::$app->user->identity->user_level == 'Admin';
        if($isAdmin) return [];

        $schoolId = School::getCurrentSchoolId();
        $studentPlans = self::find()->joinWith("plan")->andFilterWhere(['schoolsubplans.school_id' => $schoolId, 'is_active' => true])->asArray()->all();
        $planEndDates = array_map(function ($studentPlan) {
            $planPauses = StudentSubplanPauses::getForStudentSubplan($studentPlan['id'])->asArray()->all();
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
        $subplan = self::getCurrentForStudent($studentId);
        $pauses = StudentSubplanPauses::getForStudentSubplan($subplan['id'])->asArray()->all();
        $totalPausedWeeks = 0;
        foreach($pauses as $p){
            $totalPausedWeeks += $p['weeks'];
        }

        return $subplan->plan->max_pause_weeks - $totalPausedWeeks;
    }

    public function isPlanCurrentlyPaused($studentId){
        if(!StudentSubplanPauses::studentHasAnyPauses($studentId)) return false;

        $mostRecentPause = StudentSubplanPauses::getMostRecentPauseForStudent($studentId);
        $pauseStartDate = strtotime($mostRecentPause['start_date']);
        $pauseEndDate = strtotime("+" . $mostRecentPause['weeks'] . " weeks", $pauseStartDate);
        $time = time();
        
        return $pauseStartDate < $time && $pauseEndDate > $time;
    }

    public function getEndDate($studentId){
        $subplan = self::getCurrentForStudent($studentId);
        if ($subplan == null) return null;
        $planPauses = StudentSubplanPauses::getForStudentSubplan($subplan['id'])->asArray()->all();
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

    public static function shouldSendAdvanceInvoice($studentSubplan){
        if($studentSubplan === null || $studentSubplan["plan"] === null) return false;
        if(!self::isSameDayAsPlanStart($studentSubplan)) return false;
        $planMonths = $studentSubplan['plan']['months'];
        $planUnlimited = $planMonths === 0;
        $planEnded = $studentSubplan['sent_invoices_count'] == $planMonths;
        $hasPaidInAdvance = self::hasPaidInAdvance($studentSubplan);

        return ((!$planEnded || $planUnlimited) && !$hasPaidInAdvance);
    }

    public static function hasPaidInAdvance($studentSubplan){
        if($studentSubplan === null || $studentSubplan["plan"] === null) return false;
        return $studentSubplan['times_paid'] > $studentSubplan['sent_invoices_count'];
    }

    public static function isSameDayAsPlanStart($studentSubplan){
        $today = date('d.m.Y');
        $match_date = date('d.m.Y', strtotime($studentSubplan["start_date"]));

        $today_split = explode(".", $today);
        $match_date_split = explode(".", $match_date);

        return $today_split[0] === $match_date_split[0];
    }

    public function increaseSentInvoicesCount($count = 1){
        $this->sent_invoices_count += $count;
        $this->update();
    }

    public static function resetActivePlanForUser($studentId){
        $activeSubplan = self::getCurrentForStudent($studentId);

        if($activeSubplan){
            $activeSubplan->is_active = false;
            $activeSubplan->update();
        }        
    }
}

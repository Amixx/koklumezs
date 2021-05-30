<?php

namespace app\models;

use yii\data\ActiveDataProvider;

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
        return $this->hasOne(SchoolSubPlans::class, ['id' => 'plan_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    public static function getActivePlansForStudentADP($studentId)
    {
        $query =  self::find()->where(['user_id' => $studentId, 'is_active' => true])
            ->orderBy(['studentsubplans.id' => SORT_DESC])
            ->joinWith('plan');

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public static function getActivePlansForStudent($studentId)
    {
        return self::find()->where(['user_id' => $studentId, 'is_active' => true])
            ->orderBy(['studentsubplans.id' => SORT_DESC])
            ->joinWith('plan')
            ->asArray()->all();
    }

    public static function getPlanEndDatesForCurrentSchoolStudents()
    {
        $isAdmin = \Yii::$app->user->identity->user_level == 'Admin';
        if ($isAdmin) {
            return [];
        }

        $schoolId = School::getCurrentSchoolId();
        $studentPlans = self::find()->joinWith("plan")->andFilterWhere(['schoolsubplans.school_id' => $schoolId, 'is_active' => true])->asArray()->all();

        $planEndDates = array_map(function ($studentPlan) {
            $planPauses = StudentSubplanPauses::getForStudentSubplan($studentPlan['id'])->asArray()->all();
            $date = date_create($studentPlan["start_date"]);
            $date->modify("+" . $studentPlan['plan']['months'] . "month");
            foreach ($planPauses as $pause) {
                $date->modify("+" . $pause['weeks'] . "week");
            }

            return $date;
        }, $studentPlans);

        usort($planEndDates, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        $readablePlanEndDates = array_map(function ($endDate) {
            return date_format($endDate, 'd-m-Y');
        }, $planEndDates);

        return array_unique($readablePlanEndDates);
    }

    public static function getReadablePlanEndDates()
    {
        $endDates = self::getPlanEndDatesForCurrentSchoolStudents();

        $endDatesMapped = [];
        foreach ($endDates as $endDate) {
            $timestamp = strtotime($endDate);
            $readableDate = date("M", $timestamp) . " " . date("Y", $timestamp);

            $alreadyAdded = false;
            foreach ($endDatesMapped as $value) {
                if ($readableDate === $value) {
                    $alreadyAdded = true;
                }
            }

            if (!$alreadyAdded) {
                $endDatesMapped[$endDate] = $readableDate;
            }
        }

        return $endDatesMapped;
    }

    public static function getRemainingPauseWeeks($planId)
    {
        $subplan = self::findOne($planId);

        $pauses = StudentSubplanPauses::getForStudentSubplan($subplan['id'])->asArray()->all();
        $totalPausedWeeks = 0;
        foreach ($pauses as $p) {
            $totalPausedWeeks += $p['weeks'];
        }

        return $subplan->plan->max_pause_weeks - $totalPausedWeeks;
    }

    public static function isPlanCurrentlyPaused($studentId)
    {
        $activeLearningPlan = self::getLatestActiveLessonPlanForStudent($studentId);
        $mostRecentPause = StudentSubplanPauses::getMostRecentPauseForPlan($activeLearningPlan);
        if (!$mostRecentPause) return false;
        $pauseStartDate = strtotime($mostRecentPause['start_date']);
        $pauseEndDate = strtotime("+" . $mostRecentPause['weeks'] . " weeks", $pauseStartDate);
        $time = time();

        return $pauseStartDate < $time && $pauseEndDate > $time;
    }

    public static function getLearningPlanEndDateString($studentId)
    {
        $subplan = self::getLatestActiveLessonPlanForStudent($studentId);
        self::getPlanEndDateString($subplan);
    }

    public static function getPlanEndDateString($subplan)
    {
        if ($subplan == null) {
            return null;
        }
        if ($subplan['plan']['months'] == "0") {
            return \Yii::t('app',  'Unlimited');
        }
        $planPauses = StudentSubplanPauses::getForStudentSubplan($subplan['id'])->asArray()->all();
        $date = date_create($subplan["start_date"]);
        $date->modify("+" . $subplan['plan']['months'] . "month");
        foreach ($planPauses as $pause) {
            $date->modify("+" . $pause['weeks'] . "week");
        }
        $date = date_format($date, 'Y-m-d');
        $today = date('Y-m-d');
        $warningDate = date('Y-m-d', strtotime($date . ' -7 days'));

        $spanStyle = $warningDate <= $today ? "style='background: red;'" : "";

        return "<span $spanStyle>" . $date . "</span>";
    }

    public static function shouldSendAdvanceInvoice($studentSubplan)
    {
        if ($studentSubplan === null || $studentSubplan["plan"] === null || !self::isSameDayAsPlanStart($studentSubplan)) {
            return false;
        }


        $planMonths = (int)$studentSubplan['plan']['months'];
        $planUnlimited = $planMonths === 0;

        if ($planUnlimited) {
            return true;
        }

        $planEnded = $studentSubplan['sent_invoices_count'] == $planMonths;
        $hasPaidInAdvance = self::hasPaidInAdvance($studentSubplan);

        return !$planEnded && !$hasPaidInAdvance;
    }

    public static function hasPaidInAdvance($studentSubplan)
    {
        if ($studentSubplan === null || $studentSubplan["plan"] === null) {
            return false;
        }
        return $studentSubplan['times_paid'] > $studentSubplan['sent_invoices_count'];
    }

    public static function isSameDayAsPlanStart($studentSubplan)
    {
        $today = date('d.m.Y');
        $match_date = date('d.m.Y', strtotime($studentSubplan["start_date"]));

        $today_split = explode(".", $today);
        $match_date_split = explode(".", $match_date);

        return $today_split[0] === $match_date_split[0];
    }

    public static function increaseSentInvoicesCount($studentSubplan, $count = 1)
    {
        $plan = self::findOne($studentSubplan['id']);
        $plan->sent_invoices_count += $count;
        $plan->update();
    }

    public static function setStudentSubplanInactive($planId)
    {
        $plan = self::findOne($planId);
        if ($plan) {
            $plan->is_active = false;
            return $plan->update();
        }

        return false;
    }

    public static function getLatestActiveLessonPlanForStudent($studentId)
    {
        return self::getLatestOfType($studentId, 'lesson');
    }

    public static function getLatestActiveRentPlanForStudent($studentId)
    {
        return self::getLatestOfType($studentId, 'rent');
    }

    private static function getLatestOfType($studentId, $type)
    {
        return self::find()->where(['user_id' => $studentId, 'is_active' => true])
            ->joinWith('plan')
            ->orderBy(['studentsubplans.id' => SORT_DESC])
            ->andFilterWhere(['schoolsubplans.type' => $type])
            ->one();
    }
}

<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\SchoolRegistrationEmails;
use app\models\SchoolStudent;
use app\models\StartLaterCommitments;

class RegistrationCronController extends Controller
{

    const TIMES_OF_DAY = [
        'morning',
        'afternoon',
        'evening',
    ];

    public function actionMorningSessionStart()
    {
        self::sessionStart(self::TIMES_OF_DAY[0]);
    }

    public function actionAfternoonSessionStart()
    {
        self::sessionStart(self::TIMES_OF_DAY[1]);
    }

    public function actionEveningSessionStart()
    {
        self::sessionStart(self::TIMES_OF_DAY[2]);
    }

    public function actionQuarterlyReminder()
    {
        $commitments = self::getAllCommitments();

        foreach ($commitments as $commitment) {
            if ($commitment['commitment_fulfilled']) continue;

            self::setSiteLanguage($commitment);

            $emailSent = SchoolRegistrationEmails::sendEmail($commitment['user'], 'quarterly_reminder_email');

            if ($emailSent) {
                $commitmentModel = self::getCommitment($commitment['id']);
                $commitmentModel['quarterly_reminders_sent_count'] += 1;
                $commitmentModel->update();
            }
        }
    }

    public function actionEveryDayAt10()
    {
        $date = self::getCurrentDate();
        $commitments = self::getAllCommitments();

        foreach ($commitments as $commitment) {
            if ($commitment['commitment_fulfilled']) continue;

            self::setSiteLanguage($commitment);

            $dayLaterDate = self::getOneDayLaterDate($commitment['start_date']);
            $weekLaterDate = self::getOneWeekLaterDate($commitment['start_date']);

            if ($dayLaterDate === $date) {
                $emailSent = SchoolRegistrationEmails::sendEmail($commitment['user'], 'missed_session_email');

                $commitmentModel = self::getCommitment($commitment['id']);
                $commitmentModel['missed_session_email_sent'] = $emailSent;
                $commitmentModel->update();
            } else if ($weekLaterDate === $date) {
                $emailSent = SchoolRegistrationEmails::sendEmail($commitment['user'], 'week_after_missed_email');

                $commitmentModel = self::getCommitment($commitment['id']);
                $commitmentModel['week_after_missed_email_sent'] = $emailSent;
                $commitmentModel->update();
            }
        }
    }



    private static function sessionStart($timeOfDay)
    {
        $date = self::getCurrentDate();
        $commitments = self::getCommitmentsOfTimeOfDay($timeOfDay);
        $emailSent = false;

        foreach ($commitments as $commitment) {
            if ($commitment['chosen_period_started']) continue;

            self::setSiteLanguage($commitment);

            $oneDayLeftDate = self::getOneDayLeftDate($commitment['start_date']);
            $sendOneDayLeftEmail = $oneDayLeftDate === $date;

            if ($sendOneDayLeftEmail) {
                $emailSent = SchoolRegistrationEmails::sendEmail($commitment['user'], 'one_day_before_email');

                $commitmentModel = self::getCommitment($commitment['id']);
                $commitmentModel['day_before_email_sent'] = $emailSent;
                $commitmentModel->update();
            } else if ($commitment['start_date'] === $date) {
                $emailSent = SchoolRegistrationEmails::sendEmail($commitment['user'], 'half_hour_before_email');

                $commitmentModel = self::getCommitment($commitment['id']);
                $commitmentModel['half_hour_before_email_sent'] = $emailSent;
                $commitmentModel['chosen_period_started'] = true;
                $commitmentModel->update();

                $schoolStudent = SchoolStudent::findOne(['user_id' => $commitment['user_id']]);
                $schoolStudent->show_real_lessons = true;
                $schoolStudent->update();
            }
        }
    }

    private static function setSiteLanguage($commitment)
    {
        Yii::$app->language = $commitment['user'] && $commitment['user']['language'] == "lv"
            ? 'lv' : 'eng';
    }

    private static function getCurrentDate()
    {
        return date('Y-m-d');
    }

    private static function getOneDayLeftDate($start_date)
    {
        return date('Y-m-d', strtotime("-1 days", strtotime($start_date)));
    }

    private static function getOneDayLaterDate($start_date)
    {
        return date('Y-m-d', strtotime("+1 days", strtotime($start_date)));
    }

    private static function getOneWeekLaterDate($start_date)
    {
        return date('Y-m-d', strtotime("+1 weeks", strtotime($start_date)));
    }

    private static function getAllCommitments()
    {
        return StartLaterCommitments::find()
            ->joinWith('user')->asArray()->all();
    }

    private static function getCommitmentsOfTimeOfDay($timeOfDay)
    {
        return StartLaterCommitments::find()
            ->where(['start_time_of_day' => $timeOfDay])
            ->joinWith('user')->asArray()->all();
    }

    private static function getCommitment($id)
    {
        return StartLaterCommitments::findOne(['id' => $id]);
    }
}

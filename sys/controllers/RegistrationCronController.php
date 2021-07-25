<?php

namespace app\controllers;

use app\models\LectureAssignment;
use app\models\School;
use app\models\Sentlectures;
use app\models\Studentgoals;
use app\models\UserLectures;
use app\models\Lectures;
use app\models\Users;
use app\models\StudentSubPlans;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\helpers\InvoiceManager;
use app\helpers\EmailSender;
use app\models\SchoolRegistrationEmails;
use app\models\SchoolStudent;
use app\models\StartLaterCommitments;
use app\models\Trials;

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

    public function actionMorningSessionEnd()
    {
        self::sessionEnd(self::TIMES_OF_DAY[0]);
    }

    public function actionAfternoonSessionStart()
    {
        self::sessionStart(self::TIMES_OF_DAY[1]);
    }

    public function actionAfternoonSessionEnd()
    {
        self::sessionEnd(self::TIMES_OF_DAY[1]);
    }

    public function actionEveningSessionStart()
    {
        self::sessionStart(self::TIMES_OF_DAY[2]);
    }

    public function actionEveningSessionEnd()
    {
        self::sessionEnd(self::TIMES_OF_DAY[2]);
    }

    private static function sessionStart($timeOfDay)
    {
        $date = self::getCurrentDate();
        $commitments = self::getCommitmentsOfTimeOfDay($timeOfDay);

        foreach ($commitments as $commitment) {
            if ($commitment['chosen_period_started']) continue;

            $oneDayLeftDate = self::getOneDayLeftDate($commitment['start_date']);
            $sendOneDayLeftEmail = $oneDayLeftDate === $date;

            if ($sendOneDayLeftEmail) {
                self::sendEmail($commitment['user'], 'one_day_before_email');
            } else if ($commitment['start_date'] === $date) {
                self::sendEmail($commitment['user'], 'half_hour_before_email');

                $commitmentModel = StartLaterCommitments::findOne(['id' => $commitment['id']]);
                $commitmentModel['chosen_period_started'] = true;
                $commitmentModel->update();

                $schoolStudent = SchoolStudent::findOne(['user_id' => $commitment['user_id']]);
                $schoolStudent->show_real_lessons = true;
                $schoolStudent->update();
            }
        }
    }

    private static function sessionEnd($timeOfDay)
    {
        $date = self::getCurrentDate();
        $commitments = self::getCommitmentsOfTimeOfDay($timeOfDay);

        foreach ($commitments as $commitment) {
            if ($commitment['commitment_fulfilled']) continue;

            $weekLaterDate = self::getOneWeekLaterDate($commitment['start_date']);

            if ($commitment['start_date'] === $date) {
                self::sendEmail($commitment['user'], 'missed_session_email');
            } else if ($weekLaterDate === $date) {
                self::sendEmail($commitment['user'], 'week_after_missed_email');
            }
        }
    }

    private static function getCurrentDate()
    {
        return date('Y-m-d');
    }

    private static function getOneDayLeftDate($start_date)
    {
        return date('Y-m-d', strtotime("-1 days", strtotime($start_date)));
    }

    private static function getOneWeekLaterDate($start_date)
    {
        return date('Y-m-d', strtotime("+1 weeks", strtotime($start_date)));
    }

    public static function getCommitmentsOfTimeOfDay($timeOfDay)
    {
        return StartLaterCommitments::find()
            ->where(['start_time_of_day' => $timeOfDay])
            ->joinWith('user')->asArray()->all();
    }

    private static function sendEmail($user, $emailType)
    {
        $school = School::getByStudent($user['id']);
        $email = SchoolRegistrationEmails::getByType($school['id'], $emailType);

        return Yii::$app
            ->mailer
            ->compose(['html' => 'blank-message-html', 'text' => 'blank-message-text'], [
                'message' => $email,
            ])
            ->setFrom([$school['email'] => Yii::$app->name])
            ->setTo($user['email'])
            ->setSubject("TODO: paprasīt subjectu - " . Yii::$app->name)
            ->send();
    }
}

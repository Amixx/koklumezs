<?php

namespace app\helpers;

use app\models\School;
use Yii;


class EmailSender
{
    public static function sendEmailWithAdvanceInvoice($message, $schoolEmail, $studentEmail, $invoiceNumber, $invoicePath)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'rekins-html', 'text' => 'rekins-text'],
                ['message' => $message]
            )
            ->setFrom([$schoolEmail => Yii::$app->name])
            ->setTo($studentEmail)
            ->setSubject("Avansa rēķins $invoiceNumber - " . Yii::$app->name)
            ->attach($invoicePath)
            ->send();
    }

    public static function sendReminderToPay($schoolEmail, $userEmail)
    {
        return Yii::$app
            ->mailer
            ->compose(['html' => 'reminder-to-pay-html', 'text' => 'reminder-to-pay-text'])
            ->setFrom([$schoolEmail => Yii::$app->name])
            ->setTo($userEmail)
            ->setSubject("Atgādinājums par rēķina apmaksu")
            ->send();
    }

    public static function sendWarningToTeacher($studentEmail, $schoolEmail)
    {
        return Yii::$app
            ->mailer
            ->compose([
                'html' => 'invoice-not-sent-html',
                'text' => 'invoice-not-sent-text'
            ], [
                'email' => $studentEmail,
            ])
            ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
            ->setTo($schoolEmail)
            ->setSubject("Skolēnam nenosūtījās rēķins!")
            ->send();
    }

    public static function sendNewStudentNotification($user, $schoolEmail)
    {
        return Yii::$app
            ->mailer
            ->compose(['html' => 'new-user-html', 'text' => 'new-user-text'], [
                'user' => $user,
            ])
            ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
            ->setTo($schoolEmail)
            ->setSubject("Reģistrējies jauns skolēns - " . $user['first_name'])
            ->send();
    }

    public static function sendPostSignupMessage($registrationMessage, $schoolEmail, $userEmail)
    {
        return Yii::$app
            ->mailer
            ->compose(['html' => 'blank-message-html', 'text' => 'blank-message-text'], [
                'message' => $registrationMessage,
            ])
            ->setFrom([$schoolEmail => Yii::$app->name])
            ->setTo($userEmail)
            ->setSubject("Apsveicam ar reģistrēšanos - " . Yii::$app->name)
            ->send();
    }

    public static function sendInvoiceToRenter($renterMessage, $schoolEmail, $userEmail, $invoicePath)
    {
        return Yii::$app
            ->mailer
            ->compose(['html' => 'blank-message-html', 'text' => 'blank-message-text'], [
                'message' => $renterMessage,
            ])
            ->setFrom([$schoolEmail => Yii::$app->name])
            ->setTo($userEmail)
            ->setSubject("Kokles īre - " . Yii::$app->name)
            ->attach($invoicePath)
            ->send();
    }

    public static function sendRentNotification($model, $schoolEmail)
    {
        return Yii::$app
            ->mailer
            ->compose(['html' => 'instrument-html', 'text' => 'instrument-text'], [
                'model' => $model,
            ])
            ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
            ->setTo($schoolEmail)
            ->setSubject("Instrumenta īres pieteikums - " . $model['fullname'])
            ->send();
    }

    public static function sendPasswordReset($user, $userEmail)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
            ->setTo($userEmail)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();
    }

    public static function sendEmailVerification($user, $userEmail)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
            ->setTo($userEmail)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }

    public static function sendLessonNotification($user, $teacherMessage, $schoolEmail, $subject)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'lekcija-html', 'text' => 'lekcija-text'],
                [
                    'userFirstName' => $user->first_name,
                    'teacherMessage' => $teacherMessage
                ]
            )
            ->setFrom([$schoolEmail => Yii::$app->name])
            ->setTo($user->email)
            ->setSubject($subject . ' - ' . Yii::$app->name)
            ->send();
    }

    public static function sendReminderToTeacher($user, $lecture, $x, $schoolEmail)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'japieskir-lekcija-html', 'text' => 'japieskir-lekcija-text'],
                ['user' => $user, 'lecture' => $lecture, 'x' => $x]
            )
            ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name])
            ->setTo($schoolEmail)
            ->setSubject('Jāpiešķir nodarbība - ' . Yii::$app->name)
            ->send();
    }

    public static function sendTrialEndMessage($student)
    {
        $school = School::getByStudent($student['id']);
        if ($school['trial_ended_message'] != null) {
            return Yii::$app
                ->mailer
                ->compose(['html' => 'blank-message-html', 'text' => 'blank-message-text'], [
                    'message' => $school['trial_ended_message'],
                ])
                ->setFrom([$school['email'] => Yii::$app->name])
                ->setTo($student['email'])
                ->setSubject("Bedzies izmēģinājuma periods - " . Yii::$app->name)
                ->send();
        }

        return false;
    }
}

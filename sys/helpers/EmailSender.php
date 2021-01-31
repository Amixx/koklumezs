<?php

namespace app\helpers;

use Yii;

class EmailSender
{
    public static function sendEmailWithAdvanceInvoice($message, $schoolEmail, $studentEmail, $invoiceNumber, $invoicePath){
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'rekins-html', 'text' => 'rekins-text'],
                ['message' => $message])
            ->setFrom([$schoolEmail => Yii::$app->name])
            ->setTo($studentEmail)
            ->setSubject("Avansa rēķins $invoiceNumber - " . Yii::$app->name)
            ->attach($invoicePath)
            ->send();
        // return true;
    }

    public static function sendReminderToPay($schoolEmail, $userEmail){
        return Yii::$app
            ->mailer
            ->compose(['html' => 'reminder-to-pay-html', 'text' => 'reminder-to-pay-text'])
            ->setFrom([$schoolEmail => Yii::$app->name])
            ->setTo($userEmail)
            ->setSubject("Atgādinājums par rēķina apmaksu")
            ->send();
        // return true;
    }

    public static function sendWarningToTeacher($studentEmail, $schoolEmail){
        return Yii::$app
            ->mailer
            ->compose([
                'html' => 'invoice-not-sent-html', 
                'text' => 'invoice-not-sent-text'
            ], [
                'email' => $studentEmail,
            ])
            ->setFrom([$schoolEmail => Yii::$app->name])
            ->setTo($schoolEmail)
            ->setSubject("Skolēnam nenosūtījās rēķins!")
            ->send();
        // return true;
    }
}

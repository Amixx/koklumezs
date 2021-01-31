<?php

namespace app\helpers;

use app\models\School;
use app\models\SchoolTeacher;
use app\models\SentInvoices;
use app\models\StudentSubPlans;
use app\models\SchoolSubplanParts;
use app\models\Users;
use Yii;

class InvoiceManager
{
    public static function sendAdvanceInvoice($user, $studentSubplan){
        $schoolSubplan = $studentSubplan['plan'];
        $school = School::getByStudent($user['id']);
        $schoolTeacher = SchoolTeacher::getBySchoolId($school['id']);
        $userFullName = Users::getFullName($user);
        $invoiceBasePath = self::getInvoiceBasePath($schoolTeacher['user_id'], true);
        $invoiceNumber = self::generateInvoiceNumber();
        $title = self::generateInvoiceTitle($invoiceNumber, true);
        $invoicePath = $invoiceBasePath.$title;
        $subplanParts = SchoolSubplanParts::getPartsForSubplan($schoolSubplan['id']);
        $subplanCost = SchoolSubplanParts::getPlanTotalCost($schoolSubplan['id']);

        $invoiceContent = Yii::$app->view->render('@app/views/invoice-templates/advance', [
            'id' => $invoiceNumber,
            'fullName' => $userFullName,
            'email' => $user['email'],
            'subplan' => $schoolSubplan,
            'subplanCost' => $subplanCost,
            'subplanParts' => $subplanParts,
            'payer' => $user['payer'],
        ]);

        InvoicePdfFileGenerator::generate($invoicePath, $invoiceContent, $title);

        $message = isset($schoolSubplan['message']) && $schoolSubplan['message']
            ? $schoolSubplan['message']
            : "Nosūtam rēķinu par tekošā mēneša nodarbībām. Lai jauka diena!";

        $sent = EmailSender::sendEmailWithAdvanceInvoice($message, $school['email'], $user['email'], $invoiceNumber, $invoicePath);

        if ($sent) {
            $studentSubplan->increaseSentInvoicesCount();

            SentInvoices::createAdvance($user['id'], $invoiceNumber, $schoolSubplan, $studentSubplan['start_date']);
        }else{
            EmailSender::sendWarningToTeacher($user['email'], $school['email']);
        }
    }

    public static function createRealInvoice($model, $invoiceNumber, $userId, $paidDate){
        $studentSubplan = StudentSubPlans::findOne(['user_id' => $model['user_id']]);
        $studentSubplan->times_paid += 1;
        $schoolSubplan = $studentSubplan["plan"];
        $school = School::getByStudent($userId);
        $schoolTeacher = SchoolTeacher::getBySchoolId($school['id']);
        $userFullName = Users::getFullName($model['student']);      
        $invoiceBasePath = self::getInvoiceBasePath($schoolTeacher['user_id'], false);
        $title = self::generateInvoiceTitle($invoiceNumber, false);
        $invoicePath = $invoiceBasePath.$title;
        $subplanParts = SchoolSubplanParts::getPartsForSubplan($schoolSubplan['id']);
        $subplanCost = SchoolSubplanParts::getPlanTotalCost($schoolSubplan['id']);

        $invoiceContent = Yii::$app->view->render('@app/views/invoice-templates/real', [
            'number' => $invoiceNumber,
            'fullName' => $userFullName,
            'email' => $model['student']['email'],
            'subplan' => $schoolSubplan,
            'subplanCost' => $subplanCost,
            'subplanParts' => $subplanParts,
            'datePaid' => $paidDate,
            'months' => 1,
            'payer' => $model['student']['payer'],
        ]);

        InvoicePdfFileGenerator::generate($invoicePath, $invoiceContent, $title);

        SentInvoices::createReal($model['student']['id'], $invoiceNumber, $schoolSubplan, $studentSubplan['start_date'], $paidDate);

        Yii::$app->session->setFlash('success', 'Rēķina apmaksa reģistrēta!');

        $studentSubplan->save();
    }

    public static function createRealInvoiceForMultipleMonths($userId, $paidMonths, $paidDate){
        $months = intval($paidMonths, 10);
        $studentSubplan = StudentSubPlans::findOne(['user_id' => $userId]);
        $schoolSubplan = $studentSubplan["plan"];
        $user = Users::find()->where(['users.id' => $userId])->joinWith('payer')->one();
        $school = School::getByStudent($userId);
        $schoolTeacher = SchoolTeacher::getBySchoolId($school['id']);
        $invoiceBasePath = self::getInvoiceBasePath($schoolTeacher['user_id'], false);
        $invoiceNumber = self::generateInvoiceNumber();
        $title = self::generateInvoiceTitle($invoiceNumber, false);
        $invoicePath = $invoiceBasePath.$title;
        $userFullName = Users::getFullName($user);  
        $subplanParts = SchoolSubplanParts::getPartsForSubplan($schoolSubplan['id']);
        $subplanCost = SchoolSubplanParts::getPlanTotalCost($schoolSubplan['id']);

        $invoiceContent = Yii::$app->view->render('@app/views/invoice-templates/real', [
            'number' => $invoiceNumber,
            'fullName' => $userFullName,
            'email' => $user['email'],
            'subplan' => $schoolSubplan,
            'subplanCost' => $subplanCost,
            'subplanParts' => $subplanParts,
            'datePaid' => $paidDate,
            'months' => $months, 
            'payer' => $user['payer'],
        ]);

        InvoicePdfFileGenerator::generate($invoicePath, $invoiceContent, $title);

        SentInvoices::createReal($user['id'], $invoiceNumber, $schoolSubplan, $studentSubplan['start_date'], $paidDate);
        
        $studentSubplan->times_paid += $months;
        $studentSubplan->save();
    }

    public static function getInvoiceBasePath($teacherId, $isAdvance){
        $subfolderName = $isAdvance ? "advance" : "real";
        $timestamp = time();
        $folderUrl = "files/user_$teacherId/invoices/".date("M", $timestamp) . "_" . date("Y", $timestamp)."/" . $subfolderName;

        if (!is_dir($folderUrl)) mkdir($folderUrl, 0777, true);

        return $folderUrl . "/";
    }

    public static function generateInvoiceNumber(){
        return mt_rand(10000000, 99999999);
    }

    public static function generateInvoiceTitle($invoiceNumber, $isAdvance){
        $prefix = $isAdvance ? "avansa-" : "";

        return $prefix . "rekins-$invoiceNumber.pdf";
    }
}

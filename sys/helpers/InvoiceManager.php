<?php

namespace app\helpers;

use app\models\RegistrationMessage;
use app\models\School;
use app\models\SchoolTeacher;
use app\models\SentInvoices;
use app\models\StudentSubPlans;
use app\models\SchoolSubPlans;
use app\models\SchoolSubplanParts;
use app\models\Users;
use Yii;

class InvoiceManager
{
    public static function sendAdvanceInvoice($user, $studentSubplan, $sendToRenter = false)
    {
        $schoolSubplan = $studentSubplan['plan'];
        $school = School::getByStudent($user['id']);
        $invoiceNumber = self::generateInvoiceNumber();
        $destinationEmail = self::getDestinationEmail($user);

        $sent = EmailSender::sendInfoAboutNewInvoice($user, $invoiceNumber);

        if ($sent) {
            StudentSubPlans::increaseSentInvoicesCount($studentSubplan);
            SentInvoices::createAdvance($user['id'], $invoiceNumber, $schoolSubplan, $studentSubplan);
        } else {
            EmailSender::sendWarningToTeacher($destinationEmail, $school['email']);
        }
    }

    public static function createRealInvoice($model, $invoiceNumber, $userId, $paidDate, $advanceInvoice)
    {
        $school = School::getByStudent($userId);

        //pārejas periodā, kamēr ne visiem avansa rēķiniem ir `studentsubplan_id`, jāizlīdzas ar šādu risinājumu!       
        if ($advanceInvoice['studentSubplan'] !== null) {  //jaunais variants
            $studentSubplan = StudentSubPlans::findOne(['id' => $advanceInvoice['studentsubplan_id']]);
            $schoolSubplan = $studentSubplan["plan"];
        } else { // backups vecajiem
            $schoolSubplan = SchoolSubPlans::find()->where(['school_id' => $school['id'], 'name' => $advanceInvoice['plan_name']])->one();
            $studentSubplan = StudentSubPlans::find()->where(['plan_id' => $schoolSubplan['id']])->orderBy(['id' => SORT_DESC])->one();
        }

        $studentSubplan->times_paid += 1;
        $schoolTeacher = SchoolTeacher::getBySchoolId($school['id']);
        $userFullName = Users::getFullName($model['student']);
        $invoiceBasePath = self::getInvoiceBasePath($schoolTeacher['user_id'], false, strtotime($paidDate));
        $title = self::generateInvoiceTitle($invoiceNumber, false);
        $invoicePath = $invoiceBasePath . $title;
        $subplanParts = SchoolSubplanParts::getPartsForSubplan($schoolSubplan['id']);
        $subplanCost = SchoolSubplanParts::getPlanTotalCost($schoolSubplan['id']);
        $bankAccount = School::getBankAccount($school->id);

        $invoiceContent = Yii::$app->view->render('@app/views/invoice-templates/real', [
            'bankAccount' => $bankAccount,
            'number' => $invoiceNumber,
            'fullName' => $userFullName,
            'email' => self::getDestinationEmail($model['student']),
            'subplan' => $schoolSubplan,
            'subplanCost' => $subplanCost,
            'subplanParts' => $subplanParts,
            'datePaid' => $paidDate,
            'months' => 1,
            'payer' => $model['student']['payer'],
        ]);

        InvoicePdfFileGenerator::generate($invoicePath, $invoiceContent, $title);

        SentInvoices::createReal($model['student']['id'], $invoiceNumber, $schoolSubplan, $studentSubplan, $paidDate);

        Yii::$app->session->setFlash('success', \Yii::t('app', 'Invoice payment registered') . '!');

        $studentSubplan->save();
    }

    public static function createRealInvoiceForMultipleMonths($formModel)
    {
        $months = intval($formModel['paid_months_count'], 10);
        $studentSubplan = StudentSubPlans::findOne($formModel['plan_id']);
        $userId = $studentSubplan['user_id'];
        $schoolSubplan = $studentSubplan["plan"];
        $user = Users::find()->where(['users.id' => $userId, 'is_deleted' => false])->joinWith('payer')->one();
        $school = School::getByStudent($userId);
        $schoolTeacher = SchoolTeacher::getBySchoolId($school['id']);
        $invoiceBasePath = self::getInvoiceBasePath($schoolTeacher['user_id'], false, strtotime($formModel['paid_date']));
        $invoiceNumber = self::generateInvoiceNumber();
        $title = self::generateInvoiceTitle($invoiceNumber, false);
        $invoicePath = $invoiceBasePath . $title;
        $userFullName = Users::getFullName($user);
        $subplanParts = SchoolSubplanParts::getPartsForSubplan($schoolSubplan['id']);
        $subplanCost = SchoolSubplanParts::getPlanTotalCost($schoolSubplan['id']);
        $bankAccount = School::getBankAccount($school->id);

        $invoiceContent = Yii::$app->view->render('@app/views/invoice-templates/real', [
            'bankAccount' => $bankAccount,
            'number' => $invoiceNumber,
            'fullName' => $userFullName,
            'email' => self::getDestinationEmail($user),
            'subplan' => $schoolSubplan,
            'subplanCost' => $subplanCost,
            'subplanParts' => $subplanParts,
            'datePaid' => $formModel['paid_date'],
            'months' => $months,
            'payer' => $user['payer'],
        ]);

        InvoicePdfFileGenerator::generate($invoicePath, $invoiceContent, $title);

        SentInvoices::createReal($user['id'], $invoiceNumber, $schoolSubplan, $studentSubplan, $formModel['paid_date']);

        $studentSubplan->times_paid += $months;
        $studentSubplan->save();
    }

    public static function getInvoiceBasePath($teacherId, $isAdvance, $timestamp = null)
    {
        $subfolderName = $isAdvance ? "advance" : "real";
        if (!$timestamp) $timestamp = time();
        $folderUrl = "files/user_$teacherId/invoices/" . date("M", $timestamp) . "_" . date("Y", $timestamp) . "/" . $subfolderName;

        if (!is_dir($folderUrl)) mkdir($folderUrl, 0777, true);

        return $folderUrl . "/";
    }

    public static function generateInvoiceNumber()
    {
        return mt_rand(10000000, 99999999);
    }

    public static function generateInvoiceTitle($invoiceNumber, $isAdvance)
    {
        $prefix = $isAdvance ? "avansa-" : "";

        return $prefix . "rekins-$invoiceNumber.pdf";
    }

    private static function getDestinationEmail($user)
    {
        return isset($user['payer']) && $user['payer'] && $user['payer']['should_use']
            ? $user['payer']['email']
            : $user['email'];
    }
}

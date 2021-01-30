<?php

namespace app\helpers;

use app\models\School;
use app\models\SchoolTeacher;
use app\models\SchoolSubPlans;
use app\models\SentInvoices;
use kartik\mpdf\Pdf;
use Yii;

class InvoiceSender extends \yii\db\ActiveRecord
{
    public static function sendAdvanceInvoice($user, $studentSubplan){
        $inlineCss = SentInvoices::getInvoiceCss();
        $userFullName = $user['first_name'] . " " . $user['last_name'];
        $subplan = $studentSubplan["plan"];
        $planUnlimited = $subplan['months'] === 0;
        $planEnded = $studentSubplan['sent_invoices_count'] == $subplan['months'];
        $hasPaidInAdvance = $studentSubplan['times_paid'] > $studentSubplan['sent_invoices_count'];
        $school = School::getByStudent($user['id']);
        $schoolTeacher = SchoolTeacher::getBySchoolId($school['id']);
        $teacherId = $schoolTeacher['user_id'];

        if(!$planEnded || $planUnlimited){
            if(!$hasPaidInAdvance){
                $timestamp = time();
                $folderUrl = "files/user_$teacherId/invoices/".date("M", $timestamp) . "_" . date("Y", $timestamp)."/advance";
                if (!is_dir($folderUrl)) mkdir($folderUrl, 0777, true);
                $invoiceBasePath = $folderUrl . "/";

                $id = mt_rand(10000000, 99999999);
                $title = "avansa-rekins-$id.pdf";
                $invoicePath = $invoiceBasePath.$title;

                $content = Yii::$app->view->render('@app/views/invoice-templates/advance', [
                    'id' => $id,
                    'fullName' => $userFullName,
                    'email' => $user['email'],
                    'subplan' => $subplan,
                    'payer' => $user['payer'],
                ]);

                $pdf = new Pdf([
                    'mode' => Pdf::MODE_UTF8,
                    'format' => Pdf::FORMAT_A4,
                    'orientation' => Pdf::ORIENT_PORTRAIT,
                    'destination' => Pdf::DEST_FILE,
                    'filename' => $invoicePath,
                    'content' => $content,
                    'cssInline' => $inlineCss,
                    'options' => ['title' => $title],
                ]);

                $pdf->render();

                $message = "Nosūtam rēķinu par tekošā mēneša nodarbībām. Lai jauka diena!";
                if(isset($subplan['message']) && $subplan['message']) $message = $subplan['message'];
                
                $sent = Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'rekins-html', 'text' => 'rekins-text'],
                        ['message' => $message])
                    ->setFrom([$school['email'] => Yii::$app->name])
                    ->setTo($user['email'])
                    ->setSubject("Avansa rēķins $id - " . Yii::$app->name)
                    ->attach($invoicePath)
                    ->send();

                if ($sent) {
                    $studentSubplan['sent_invoices_count'] += 1;
                    $studentSubplan->update();

                    $invoice = new SentInvoices;
                    $invoice->user_id = $user['id'];
                    $invoice->invoice_number = $id;
                    $invoice->is_advance = true;
                    $invoice->plan_name = $subplan['name'];
                    $invoice->plan_price = $subplan['monthly_cost'];
                    $invoice->plan_start_date = $studentSubplan['start_date'];
                    $invoice->save();
                }else{
                    Yii::$app
                        ->mailer
                        ->compose([
                            'html' => 'invoice-not-sent-html', 
                            'text' => 'invoice-not-sent-text'
                        ], [
                            'email' => $user['email'],
                        ])
                        ->setFrom([$school['email'] => Yii::$app->name])
                        ->setTo($school['email'])
                        ->setSubject("Skolēnam nenosūtījās rēķins!")
                        ->send();
                }
            }else{
                $studentSubplan['sent_invoices_count'] += 1;
                $studentSubplan->update();
            }
        }
    }
}

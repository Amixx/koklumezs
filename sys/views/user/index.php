<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\StudentSubplanPauses;
use app\models\SentInvoices;
use app\models\SchoolSubplanParts;
use app\models\StudentSubplans;

$this->title = \Yii::t('app',  'Users');


$planEndMonths = [];
?>
<div class="user-index">
    <h1><?= Yii::t('app', 'Students') ?></h1>
    <p>
        <?= Html::a(\Yii::t('app',  'Create user'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    $status = [10 => \Yii::t('app',  'Active'), 9 => \Yii::t('app',  'Inactive'), 0 => \Yii::t('app',  'Deleted')];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'first_name',
            'last_name',
            [
                'attribute' => 'subscription_type',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    if ($dataProvider->subscription_type == 'free') {
                        return \Yii::t('app',  'For free');
                    } else if ($dataProvider->subscription_type == 'paid') {
                        return \Yii::t('app',  'Paid');
                    } else {
                        return \Yii::t('app',  'Lead');
                    }
                },
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subscription_type]',
                    isset($get['TeacherUserSearch']['subscription_type'])
                        ? $get['TeacherUserSearch']['subscription_type']
                        : '',
                    app\models\Users::getSubscriptionTypes(),
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    if ($dataProvider->status == '10') {
                        return "<span style='color:green;'>" . \Yii::t('app',  'Active') . "</span>";
                    } else if ($dataProvider->status == '9') {
                        return "<span style='color:red;'>" . \Yii::t('app',  'Inactive') . "</span>";
                    } else {
                        return "<span>" . \Yii::t('app',  'Passive') . "</span>";
                    }
                },
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[status]',
                    isset($get['TeacherUserSearch']['status'])
                        ? $get['TeacherUserSearch']['status']
                        : '',
                    app\models\Users::getStatus(),
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'Plan price',
                'label' => Yii::t('app', 'Payment'),
                'value' => function ($dataProvider) {
                    $studentSubplan = StudentSubplans::getCurrentForStudent($dataProvider['id']);
                    if (!$studentSubplan || !$studentSubplan["plan"]) {
                        return;
                    }

                    $planId = $studentSubplan["plan_id"];
                    $totalCost = SchoolSubplanParts::getPlanTotalCost($planId);
                    $url = Url::to(['school-sub-plans/view', 'id' => $planId]);
                    return "<a href='" . $url . "'>$totalCost</a>";
                },
                'format' => 'html',
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subplan_monthly_cost]',
                    isset($get['TeacherUserSearch']['subplan_monthly_cost'])
                        ? $get['TeacherUserSearch']['subplan_monthly_cost']
                        : '',
                    $schoolSubPlanPrices,
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'Plan end date',
                'label' => Yii::t('app', 'Plan end date'),
                'value' => function ($dataProvider) {
                    $studentSubplan = StudentSubplans::getCurrentForStudent($dataProvider['id']);
                    if (!$studentSubplan || !$studentSubplan['plan']) {
                        return;
                    }
                    if ($studentSubplan['plan']['months'] == '0') {
                        return \Yii::t('app',  'Unlimited');
                    }
                    $planPauses = StudentSubplanPauses::getForStudentSubplan($studentSubplan['id'])->asArray()->all();
                    $date = date_create($studentSubplan["start_date"]);
                    $date->modify("+" . $studentSubplan['plan']['months'] . "month");
                    foreach ($planPauses as $pause) {
                        $date->modify("+" . $pause['weeks'] . "week");
                    }
                    return date_format($date, 'd-m-Y');
                },
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subplan_end_date]',
                    isset($get['TeacherUserSearch']['subplan_end_date'])
                        ? $get['TeacherUserSearch']['subplan_end_date']
                        : '',
                    $planEndDates,
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']
                ),
                'format' => 'raw'
            ],
            [
                'attribute' => 'Payments',
                'label' => Yii::t('app', 'Paid/Has to pay'),
                'value' => function ($dataProvider) {
                    $studentSubplan = StudentSubplans::getCurrentForStudent($dataProvider['id']);
                    $unpaidInvoiceNumbers = SentInvoices::getUnpaidForStudent($dataProvider["id"]);

                    if (!$studentSubplan && !$unpaidInvoiceNumbers) {
                        return;
                    }

                    $invoice = SentInvoices::getLatestForStudent($dataProvider['id']);
                    $studentId = $dataProvider['id'];

                    $html = "";
                    $addPaymentHtml = "";

                    if ($studentSubplan) {
                        $color = "#99ff9c";
                        if ($studentSubplan["times_paid"] < $studentSubplan["sent_invoices_count"]) {
                            $color = "#ff9a99";
                        }
                        if ($studentSubplan["times_paid"] > $studentSubplan["sent_invoices_count"]) {
                            $color = "#99cfff";
                        }

                        if (isset($invoice)) {
                            $is_advance = $invoice['is_advance'];
                            $invoiceSentDate = $invoice['sent_date'];
                            $today = date('Y-m-d');
                            $warningDate = date('Y-m-d', strtotime($invoiceSentDate . ' +14 days'));
                            if ($is_advance && $today <= $warningDate) {
                                $color = "#cb7119";
                            }
                        }

                        $timesPaid = $studentSubplan["times_paid"];
                        $sentInvoices = $studentSubplan["sent_invoices_count"];
                        $html .= "<div style='text-align:center;background:" . $color . "'>" . $timesPaid . "/" . $sentInvoices . "</div>";
                        $url = Url::to(['sent-invoices/register-advance-payment', 'userId' => $studentId]);
                        $addPaymentHtml = "<span title='Reģistrēt maksājumu'>
                            <a
                                href='" . $url . "'
                                class='glyphicon glyphicon-plus'
                            ></a></span>";
                    }

                    if ($unpaidInvoiceNumbers) {
                        $html .= "<p>Neapmaksātie rēķini: </p>";
                        foreach ($unpaidInvoiceNumbers as $number) {
                            $value = $number['invoice_number'];
                            $url = Url::to(['sent-invoices/update', 'invoiceNumber' => $value]);
                            $html .= "
                            <p>
                                <a target='_blank' href='" . $url . "'><strong>$value</strong></a>
                            </p>
                            ";
                        }
                    }

                    $url = Url::to(['cron/remind-to-pay', 'userId' => $studentId]);

                    return "
                        <div style='text-align:center;'>
                            $html
                            <span style='margin-right:48px;'><a
                                href='" . $url . "'
                                class='glyphicon glyphicon-envelope'                                
                                title='Nosūtīt atgādinājumu, ka jāmaksā'
                            ></a></span>
                            $addPaymentHtml
                        </div>
                    ";
                },
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subplan_paid_type]',
                    isset($get['TeacherUserSearch']['subplan_paid_type']) ? $get['TeacherUserSearch']['subplan_paid_type'] : '',
                    [
                        'late' => Yii::t('app', 'Late'),
                        'paid' => Yii::t('app', 'All paid'),
                        'prepaid' => Yii::t('app', 'Prepaid'),
                    ],
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']
                ),
                'format' => 'html',
            ],
            [
                'attribute' => 'Chat',
                'label' => Yii::t('app', 'Chat'),
                'value' => function ($dataProvider) {
                    if (!$dataProvider) {
                        return;
                    }

                    $userId = $dataProvider['id'];
                    return "<span data-userid='$userId' style='width: 41px;' class='btn btn-success glyphicon glyphicon-envelope chat-with-student'>&nbsp;</span>";
                },
                'format' => 'raw',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<?php

/* @var bool $isFitnessSchool */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\StudentSubplanPauses;
use app\models\SentInvoices;
use app\models\SchoolSubplanParts;
use app\models\StudentSubPlans;
use app\models\SchoolSubPlans;
use app\models\LectureViews;

$this->title = \Yii::t('app', 'Users');
?>
<div class="user-index">
    <h1><?= Yii::t('app', 'Students') ?></h1>
    <p>
        <?= Html::a(\Yii::t('app', 'Create user'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <p>
        <?= Html::a(\Yii::t('app', 'Students who have registered but have not started playing yet'), ['recently-registered-students/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    <p>
        <?= Html::a(\Yii::t('app', 'Student "commitments" to start later'), ['start-later-commitments/index'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php
    $status = [10 => \Yii::t('app', 'Active'), 9 => \Yii::t('app', 'Inactive'), 0 => \Yii::t('app', 'Deleted')];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{view} {update} {delete} {update-client-data}',
                'buttons' => [
                    'update-client-data' => function ($url, $model, $key) use ($isFitnessSchool) {
                        return $isFitnessSchool
                            ? Html::a(
                                '<i class="glyphicon glyphicon-stats"></i>', Url::to(['client-data/update', 'userId' => $model->id]),
                                [
                                    'title' => \Yii::t('app', 'Edit client data'),
                                ]
                            )
                            : '';
                    },
                ]
            ],
            'first_name',
            'last_name',
            [
                'attribute' => 'subscription_type',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    if ($dataProvider->subscription_type == 'free') {
                        return \Yii::t('app', 'For free');
                    } else if ($dataProvider->subscription_type == 'paid') {
                        return \Yii::t('app', 'Paid');
                    } else {
                        return \Yii::t('app', 'Lead');
                    }
                },
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subscription_type]',
                    isset($get['TeacherUserSearch']['subscription_type'])
                        ? \Yii::t('app', $get['TeacherUserSearch']['subscription_type'])
                        : '',
                    app\models\Users::getSubscriptionTypes(),
                    ['prompt' => '-- ' . \Yii::t('app', 'Show all') . ' --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    if ($dataProvider->status == '10') {
                        return "<span style='color:green;'>" . \Yii::t('app', 'Active') . "</span>";
                    } else if ($dataProvider->status == '9') {
                        return "<span style='color:red;'>" . \Yii::t('app', 'Inactive') . "</span>";
                    } else {
                        return "<span>" . \Yii::t('app', 'Passive') . "</span>";
                    }
                },
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[status]',
                    isset($get['TeacherUserSearch']['status'])
                        ? $get['TeacherUserSearch']['status']
                        : '',
                    app\models\Users::getStatus(),
                    ['prompt' => '-- ' . \Yii::t('app', 'Show all') . ' --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'lectureviews',
                'label' => Yii::t('app', '30 day l. views'),
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return LectureViews::getDayResult($dataProvider->id, 30);
                },
            ],
            [
                'attribute' => 'Plan price',
                'label' => Yii::t('app', 'Payment'),
                'value' => function ($dataProvider) {
                    $studentSubplan = StudentSubplans::getLatestActiveLessonPlanForStudent($dataProvider['id']);
                    if (!$studentSubplan || !$studentSubplan["plan"]) return;

                    $planId = $studentSubplan["plan_id"];
                    $totalCost = SchoolSubplanParts::getPlanTotalCost($planId);
                    return "<a href='/sys/school-sub-plans/view?id=$planId'>$totalCost</a>";
                },
                'format' => 'html',
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subplan_monthly_cost]',
                    isset($get['TeacherUserSearch']['subplan_monthly_cost'])
                        ? $get['TeacherUserSearch']['subplan_monthly_cost']
                        : '',
                    $schoolSubPlanPrices,
                    ['prompt' => '-- ' . \Yii::t('app', 'Show all') . ' --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'Lesson plan end date',
                'label' => Yii::t('app', 'Lesson plan end date'),
                'value' => function ($dataProvider) {
                    $latestLessonPlan = StudentSubplans::getLatestActiveLessonPlanForStudent($dataProvider['id']);

                    if (!$latestLessonPlan || !$latestLessonPlan['plan']) {
                        return \Yii::t('app', 'No lesson plan');
                    }
                    if ($latestLessonPlan['plan']['months'] == '0') {
                        return \Yii::t('app', 'Unlimited');
                    }
                    $planPauses = StudentSubplanPauses::getForStudentSubplan($latestLessonPlan['id'])->asArray()->all();
                    $date = date_create($latestLessonPlan["start_date"]);
                    $date->modify("+" . $latestLessonPlan['plan']['months'] . "month");
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
                    ['prompt' => '-- ' . \Yii::t('app', 'Show all') . ' --', 'class' => 'form-control']
                ),
                'format' => 'raw'
            ],
            [
                'attribute' => 'Payments',
                'label' => Yii::t('app', 'Paid/Has to pay'),
                'value' => function ($dataProvider) {
                    $studentSubplans = StudentSubPlans::getActivePlansForStudent($dataProvider['id']);
                    $unpaidInvoiceNumbers = SentInvoices::getUnpaidForStudent($dataProvider["id"]);

                    if (!$studentSubplans && !$unpaidInvoiceNumbers) {
                        return;
                    }

                    $invoice = SentInvoices::getLatestForStudent($dataProvider['id']);
                    $studentId = $dataProvider['id'];

                    $html = "";
                    $addPaymentHtml = "";

                    foreach ($studentSubplans as $studentSubplan) {
                        if ($studentSubplan) {
                            $color = "#99ff9c";
                            $isLate = $studentSubplan["times_paid"] < $studentSubplan["sent_invoices_count"];
                            $hasPaidInAdvance = $studentSubplan["times_paid"] > $studentSubplan["sent_invoices_count"];

                            if ($isLate) {
                                $color = "#ff9a99";
                            }
                            if ($hasPaidInAdvance) {
                                $color = "#99cfff";
                            }

                            if (isset($invoice)) {
                                $is_advance = $invoice['is_advance'];
                                $invoiceSentDate = $invoice['sent_date'];
                                $today = date('Y-m-d');
                                $warningDate = date('Y-m-d', strtotime($invoiceSentDate . ' +14 days'));
                                if ($is_advance && $studentSubplan["times_paid"] != $studentSubplan["sent_invoices_count"] && $today <= $warningDate) {
                                    $color = "#cb7119";
                                }
                            }

                            $timesPaid = $studentSubplan["times_paid"];
                            $sentInvoices = $studentSubplan["sent_invoices_count"];
                            $planType = "(Dzēsts plāns)";
                            $planModel = SchoolSubPlans::findOne($studentSubplan['plan']['id']);

                            if ($planModel) {
                                $planType = $planModel->typeText();
                            }

                            $urlToEditPlan = Url::to(['student-sub-plans/update', 'id' => $studentSubplan['id']]);
                            $urlToSendReminder = Url::to(['cron/remind-to-pay', 'studentSubplanId' => $studentSubplan['id']]);
                            $remindToPayHtml = $isLate
                                ? "<span style='margin-left:16px;'>
                                    <a
                                        href='" . $urlToSendReminder . "'
                                        class='glyphicon glyphicon-envelope'                                
                                        title='" . \Yii::t('app', 'Send invoice reminder') . "'
                                    ></a>
                                </span>"
                                : "";

                            $html .= "<div style='text-align:center;background:$color'>
                                <span>$planType: $timesPaid/$sentInvoices</span> 
                                <a href='$urlToEditPlan'><span class='glyphicon glyphicon-pencil'></span></a>
                                $remindToPayHtml
                            </div>";

                            $url = Url::to(['sent-invoices/register-advance-payment', 'userId' => $studentId]);
                            $addPaymentHtml = "<span title='" . \Yii::t('app', 'Register payment') . "'>
                            <a
                                href='" . $url . "'
                                class='glyphicon glyphicon-plus'
                            ></a></span>";
                        }
                    }

                    if ($unpaidInvoiceNumbers) {
                        $html .= "<p>" . \Yii::t('app', 'Unpaid invoices') . ": </p>";
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

                    return "
                        <div style='text-align:center;'>
                            $html                            
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
                    ['prompt' => '-- ' . \Yii::t('app', 'Show all') . ' --', 'class' => 'form-control']
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
        ],
    ]); ?>
</div>
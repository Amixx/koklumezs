<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\StudentSubplanPauses;
use app\models\SentInvoices;
use  yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TeacherUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $user app\models\Users */

$this->title = \Yii::t('app',  'Users');
$this->params['breadcrumbs'][] = $this->title;

$planEndMonths = [];
?>
<div class="user-index">
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
                'filter' => Html::dropDownList('TeacherUserSearch[subscription_type]', isset($get['TeacherUserSearch']['subscription_type']) ? $get['TeacherUserSearch']['subscription_type'] : '', app\models\Users::getSubscriptionTypes(), ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
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
                'filter' => Html::dropDownList('TeacherUserSearch[status]', isset($get['TeacherUserSearch']['status']) ? $get['TeacherUserSearch']['status'] : '', app\models\Users::getStatus(), ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
             [
                'attribute' => 'Plan price',
                'label' => Yii::t('app', 'Payment'),
                'value' => function ($dataProvider) {
                    if(!$dataProvider["subplan"] || !$dataProvider["subplan"]["plan"]) return;
                    return "<a href='/sys/school-sub-plans/view?id=".$dataProvider["subplan"]["plan"]["id"]."'>".$dataProvider["subplan"]["plan"]["monthly_cost"]."</a>";
                },
                'format' => 'html',
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subplan_monthly_cost]',
                    isset($get['TeacherUserSearch']['subplan_monthly_cost']) ? $get['TeacherUserSearch']['subplan_monthly_cost'] : '',
                    $schoolSubPlanPrices,
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'Plan end date',
                'label' => Yii::t('app', 'Plan end date'),
                'value' => function ($dataProvider) {
                    if(!$dataProvider['subplan'] || !$dataProvider['subplan']['plan'] || !$dataProvider['subplan']['plan']['months']) return;
                    $planPauses = StudentSubplanPauses::getForStudent($dataProvider['subplan']['user_id'])->asArray()->all();
                    $date = date_create($dataProvider["subplan"]["start_date"]);
                    $date->modify("+" . $dataProvider['subplan']['plan']['months'] . "month");
                    foreach($planPauses as $pause){
                        $date->modify("+" . $pause['weeks'] . "week");
                    }
                    return date_format($date, 'd-m-Y');
                },
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subplan_end_date]',
                    isset($get['TeacherUserSearch']['subplan_end_date']) ? $get['TeacherUserSearch']['subplan_end_date'] : '',
                    $planEndDates,
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
                'format' => 'raw'
            ],
            [
                'attribute' => 'Payments',
                'label' => Yii::t('app', 'Paid/Has to pay'),
                'value' => function ($dataProvider) {
                    if(!$dataProvider['subplan']) return;

                    $color = "#99ff9c";
                    if($dataProvider['subplan']["times_paid"] < $dataProvider['subplan']["sent_invoices_count"]) $color = "#ff9a99";
                    if($dataProvider['subplan']["times_paid"] > $dataProvider['subplan']["sent_invoices_count"]) $color = "#99cfff";
                    $unpaidInvoiceNumbers = SentInvoices::getUnpaidForStudent($dataProvider["id"]);
                    $studentId = $dataProvider['id'];
                    $timesPaid = $dataProvider['subplan']["times_paid"];
                    $sentInvoices = $dataProvider['subplan']["sent_invoices_count"];

                    $html = "";
                    if($unpaidInvoiceNumbers){   
                        foreach($unpaidInvoiceNumbers as $number){
                            $value = $number['invoice_number'];
                            $html .= "
                            <p>
                                <a target='_blank' href='/sys/sent-invoices/update?invoiceNumber=$value'><strong>$value</strong></a>
                            </p>
                            ";
                        }
                    }
                    
                    return "
                        <div style='text-align:center;background:" . $color . "'>" . $timesPaid . "/" . $sentInvoices . "</div>
                        <div style='text-align:center;'>
                            <p>Neapmaksātie rēķini: </p>
                            $html
                            <span style='margin-right:48px;'><a
                                href='/sys/cron/remind-to-pay?userId=$studentId'
                                class='glyphicon glyphicon-envelope'                                
                                title='Nosūtīt atgādinājumu, ka jāmaksā'
                            ></a></span>
                            <span title='Reģistrēt maksājumu'>
                                <a href='/sys/sent-invoices/register-advance-payment?userId=$studentId' class='glyphicon glyphicon-plus'></a>
                            </span>
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
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
                'format' => 'html',
            ],
            [
                'attribute' => 'Chat',
                'label' => Yii::t('app', 'Chat'),
                'value' => function ($dataProvider) {
                    if(!$dataProvider) return;

                    $userId = $dataProvider['id'];
                    return "<span data-userid='$userId' style='width: 41px;' class='btn btn-success glyphicon glyphicon-envelope chat-with-student'>&nbsp;</span>";
                },
                'format' => 'raw',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<?php

use yii\helpers\Html;
use yii\grid\GridView;

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
            'username',
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
                'attribute' => 'Plan name',
                'label' => Yii::t('app', 'Plan name'),
                'value' => function ($dataProvider) {
                    return "<a href='/sys/school-sub-plans/view?id=".$dataProvider["subplan"]["plan"]["id"]."'>".$dataProvider["subplan"]["plan"]["name"]."</a>";
                },
                'format' => 'html',
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subplan_name]',
                    isset($get['TeacherUserSearch']['subplan_name']) ? $get['TeacherUserSearch']['subplan_name'] : '',
                    $schoolSubPlans,
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
             [
                'attribute' => 'Plan end date',
                'label' => Yii::t('app', 'Plan end date'),
                'value' => function ($dataProvider) {
                    if(!$dataProvider['subplan']['plan']['months']) return;
                    $date = date_create($dataProvider["subplan"]["start_date"]);
                    $date->modify("+" . $dataProvider['subplan']['plan']['months'] . "month");
                    return date_format($date, 'd-m-Y');
                },
                'filter' => Html::dropDownList(
                    'TeacherUserSearch[subplan_end_date]',
                    isset($get['TeacherUserSearch']['subplan_end_date']) ? $get['TeacherUserSearch']['subplan_end_date'] : '',
                    $planEndDates,
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
                'format' => 'raw'
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TeacherUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $user app\models\Users */

$this->title = \Yii::t('app',  'Users');
$this->params['breadcrumbs'][] = $this->title;
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
                'attribute' => 'user_level',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    switch ($dataProvider->user_level) {
                        case 'Student':
                            return \Yii::t('app',  'Student');
                            break;
                        case 'Admin':
                            return \Yii::t('app',  'Administrator');
                            break;
                        case 'Teacher':
                            return \Yii::t('app',  'Teacher');
                            break;
                    }
                },
                'filter' => Html::dropDownList('TeacherUserSearch[user_level]', isset($get['TeacherUserSearch']['user_level']) ? $get['TeacherUserSearch']['user_level'] : '', app\models\Users::getLevels(), ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
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
                'attribute' => 'last_login',
                'value' => 'last_login',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'last_login',
                    'language' => 'lv',
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            [
                'attribute' => 'dont_bother',
                'value' => 'dont_bother',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'dont_bother',
                    'language' => 'lv',
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
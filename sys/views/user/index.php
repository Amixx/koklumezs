<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $user app\models\Users */

$this->title = 'Lietotāji';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <p>
        <?= Html::a('Izveidot lietotāju', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    $status = [10 => 'Aktīvs', 9 => 'Nav aktīvs', 0 => 'Dzēsts'];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'first_name',
            'last_name',
            'phone_number',
            'email:email',
            [
                'attribute' => 'user_level',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    switch ($dataProvider->user_level) {
                        case 'Student':
                            return 'Students';
                            break;

                        case 'Admin':
                            return 'Administrators';
                            break;
                        case 'Teacher':
                            return 'Skolotājs';
                            break;
                    }
                },
                'filter' => Html::dropDownList('UserSearch[user_level]', isset($get['UserSearch']['user_level']) ? $get['UserSearch']['user_level'] : '', app\models\Users::getLevels(), ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'language',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->language == 'lv' ? 'Latviešu' : 'Angļu';
                },
                'filter' => Html::dropDownList('UserSearch[language]', isset($get['UserSearch']['language']) ? $get['UserSearch']['language'] : '', app\models\Users::getLanguages(), ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'subscription_type',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    if ($dataProvider->subscription_type == 'free') {
                        return "Par brīvu";
                    } else if ($dataProvider->subscription_type == 'paid') {
                        return "Par maksu";
                    } else {
                        return "Izmēģina";
                    }
                },
                'filter' => Html::dropDownList('UserSearch[subscription_type]', isset($get['UserSearch']['subscription_type']) ? $get['UserSearch']['subscription_type'] : '', app\models\Users::getSubscriptionTypes(), ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    if ($dataProvider->status == '10') {
                        return '<span style="color:green;">Aktīvs</span>';
                    } else if ($dataProvider->status == '9') {
                        return '<span style="color:red;">Nav aktīvs</span>';
                    } else {
                        return '<span>Pasīvs</span>';
                    }
                },
                'filter' => Html::dropDownList('UserSearch[status]', isset($get['UserSearch']['status']) ? $get['UserSearch']['status'] : '', app\models\Users::getStatus(), ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
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
                'attribute' => 'last_lecture',
                'format' => 'raw',
                'value' => 'lecture.title',
                'filter' => Html::dropDownList('UserSearch[last_lecture]', isset($get['UserSearch']['last_lecture']) ? $get['UserSearch']['last_lecture'] : '', $lectures, ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
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
            [
                'attribute' => 'allowed_to_download_files',
                'value' => 'allowed_to_download_files',
                'filter' => Html::dropDownList('UserSearch[allowed_to_download_files]', isset($get['UserSearch']['allowed_to_download_files']) ? $get['UserSearch']['allowed_to_download_files'] : '', [0 => "Nē", 1 => "Jā"], ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserLecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Piešķirtās nodarbības';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-lectures-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Piešķirt nodarbību', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'lecture_id',
                'format' => 'raw',
                'value' => 'lecture.title',
                'filter' => Html::dropDownList('UserLecturesSearch[lecture_id]', isset($get['UserLecturesSearch']['lecture_id']) ? $get['UserLecturesSearch']['lecture_id'] : '', $lectures, ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => 'student.email',
                'filter' => Html::dropDownList('UserLecturesSearch[user_id]', isset($get['UserLecturesSearch']['user_id']) ? $get['UserLecturesSearch']['user_id'] : '', $students, ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'assigned',
                'format' => 'raw',
                'value' => 'admin.email',
                'filter' => Html::dropDownList('UserLecturesSearch[assigned]', isset($get['UserLecturesSearch']['assigned']) ? $get['UserLecturesSearch']['assigned'] : '', $admins, ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'created',
                'value' => 'created',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created',
                    'language' => 'lv',
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            [
                'attribute' => 'opened',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->opened == 1 ? 'Jā' : 'Nē';
                },
                'filter' => Html::dropDownList('UserLecturesSearch[opened]', isset($get['UserLecturesSearch']['opened']) ? $get['UserLecturesSearch']['opened'] : '', [0 => 'Nav atvērta', 1 => 'Atvērta'], ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'opentime',
                'value' => 'opentime',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'opentime',
                    'language' => 'lv',
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            [
                'attribute' => 'sent',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->sent == 1 ? 'Jā' : 'Nē';
                },
                'filter' => Html::dropDownList('UserLecturesSearch[sent]', isset($get['UserLecturesSearch']['sent']) ? $get['UserLecturesSearch']['sent'] : '', [0 => 'Nav', 1 => 'Ir'], ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'evaluated',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->evaluated == 1 ? 'Jā' : 'Nē';
                },
                'filter' => Html::dropDownList('UserLecturesSearch[evaluated]', isset($get['UserLecturesSearchevaluatedsent']) ? $get['UserLecturesSearch']['evaluated'] : '', [0 => 'Nav', 1 => 'Ir'], ['prompt' => '-- Rādīt visus --', 'class' => 'form-control']),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
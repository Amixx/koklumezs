<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SentlecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app',  'Sent e-mails');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sentlectures-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => 'student.username',
                'filter' => Html::dropDownList('SentlecturesSearch[user_id]', isset($get['SentlecturesSearch']['user_id']) ? $get['SentlecturesSearch']['user_id'] : '', $students, ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'lecture_id',
                'format' => 'raw',
                'value' => 'lecture.title',
                'filter' => Html::dropDownList('SentlecturesSearch[lecture_id]', isset($get['SentlecturesSearch']['lecture_id']) ? $get['SentlecturesSearch']['lecture_id'] : '', $lectures, ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'sent',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->sent == 1 ? \Yii::t('app',  'Yes') : \Yii::t('app',  'No');
                },
                'filter' => Html::dropDownList('SentlecturesSearch[sent]', isset($get['SentlecturesSearch']['sent']) ? $get['SentlecturesSearch']['sent'] : '', [0 => 'Nav nosūtīts', 1 => 'Nosūtīts'], ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
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
            //'user_id',
            //'lecture_id',
            //'sent',
            //'created',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
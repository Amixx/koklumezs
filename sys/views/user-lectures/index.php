<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserLecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app',  'Assigned lessons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-lectures-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app',  'Assign lesson'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'lecture_id',
                'format' => 'raw',
                'value' => 'lecture.title',
                'filter' => Html::dropDownList('UserLecturesSearch[lecture_id]', isset($get['UserLecturesSearch']['lecture_id']) ? $get['UserLecturesSearch']['lecture_id'] : '', $lectures, ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => 'student.username',
                'filter' => Html::dropDownList('UserLecturesSearch[user_id]', isset($get['UserLecturesSearch']['user_id']) ? $get['UserLecturesSearch']['user_id'] : '', $students, ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'assigned',
                'format' => 'raw',
                'value' => 'admin.username',
                'filter' => Html::dropDownList('UserLecturesSearch[assigned]', isset($get['UserLecturesSearch']['assigned']) ? $get['UserLecturesSearch']['assigned'] : '', $admins, ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
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
                    return $dataProvider->opened == 1 ? \Yii::t('app',  'Yes') : \Yii::t('app',  'No');
                },
                'filter' => Html::dropDownList('UserLecturesSearch[opened]', isset($get['UserLecturesSearch']['opened']) ? $get['UserLecturesSearch']['opened'] : '', [0 => \Yii::t('app',  'Not opened'), 1 => \Yii::t('app',  'Opened')], ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
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
                    return $dataProvider->sent == 1 ? \Yii::t('app',  'Yes') : \Yii::t('app',  'No');
                },
                'filter' => Html::dropDownList('UserLecturesSearch[sent]', isset($get['UserLecturesSearch']['sent']) ? $get['UserLecturesSearch']['sent'] : '', [0 => \Yii::t('app',  'Isn\'t'), 1 => \Yii::t('app',  'Is')], ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'evaluated',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->evaluated == 1 ? \Yii::t('app',  'Yes') : \Yii::t('app',  'No');
                },
                'filter' => Html::dropDownList('UserLecturesSearch[evaluated]', isset($get['UserLecturesSearchevaluatedsent']) ? $get['UserLecturesSearch']['evaluated'] : '', [0 => \Yii::t('app',  'Isn\'t'), 1 => \Yii::t('app',  'Is')], ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"> </span>',
                            $url,
                            ['title' => 'View', 'data-pjax' => '0', 'onclick' => "window.open('" . $url . "','newwindow','width=1200,height=1200');return false;"]
                        );
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = '/sys/lekcijas/lekcija/' . $model['lecture_id'] . '?force=1';
                        return $url;
                    }
                }
            ],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}'],
        ],
    ]); ?>


</div>
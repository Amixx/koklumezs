<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;

$this->title = \Yii::t('app',  'Metrics');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="userlectureevaluations-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?= \Yii::t('app', 'Student evaluations') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="params-tab" data-toggle="tab" href="#params" role="tab" aria-controls="params" aria-selected="false"><?= \Yii::t('app', 'Sent invoices') ?></a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'lecture_id',
                        'format' => 'raw',
                        'value' => 'lecture.title',
                        'filter' => Html::dropDownList('UserlectureevaluationsSearch[lecture_id]', isset($get['UserlectureevaluationsSearch']['lecture_id']) ? $get['UserlectureevaluationsSearch']['lecture_id'] : '', $lectures, ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
                    ],
                    [
                        'attribute' => 'evaluation_id',
                        'format' => 'raw',
                        'value' => 'evalua.title',
                        'filter' => Html::dropDownList('UserlectureevaluationsSearch[evaluation_id]', isset($get['UserlectureevaluationsSearch']['evaluation_id']) ? $get['UserlectureevaluationsSearch']['evaluation_id'] : '', $evaluations, ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
                        'content' => function ($data) {
                            return Yii::t('app', $data["evalua"]["title"]);
                        }
                    ],
                    [
                        'attribute' => 'user_id',
                        'format' => 'raw',
                        'value' => 'student.email',
                        'filter' => Html::dropDownList('UserlectureevaluationsSearch[user_id]', isset($get['UserlectureevaluationsSearch']['user_id']) ? $get['UserlectureevaluationsSearch']['user_id'] : '', $students, ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
                    ],
                    'evaluation:ntext',
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
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-eye-open"> </span>',
                                    $url,
                                    ['title' => 'View', 'data-pjax' => '0']
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
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}']
                ],
            ]);
            ?>
        </div>
    </div>

    

    <?php if (!$isTeacher) {
        echo GridView::widget([
            'dataProvider' => $commentResponsesDataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'author.email',
                'text',
                'created',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-eye-open"> </span>',
                                $url,
                                ['title' => 'View', 'data-pjax' => '0']
                            );
                        },
                    ],
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'view') {
                            $url = '/sys/lekcijas/lekcija/' . $model['userlectureevaluation']['lecture_id'] . '?force=1';
                            return $url;
                        }

                        // if ($action === 'update') {
                        //     $url = '/sys/lectures/update/' . $model->id;
                        //     return $url;
                        // }
                        // if ($action === 'delete') {
                        //     $url = '/sys/lectures/delete/' . $model->id;
                        //     return $url;
                        // }
                    }
                ],
            ],
        ]);
    }
    ?>
</div>
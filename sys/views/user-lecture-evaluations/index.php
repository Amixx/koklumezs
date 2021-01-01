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
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <label for="sent-invoices-filter"><?= Yii::t("app", "Search") ?>&nbsp;(<?= Yii::t("app", "enter at least 4 symbols") ?>): 
                <input type="text" name="sent-invoices-filter" class="form-control">
            </label>
            <label for="invoices-year-selector">Meklēt pēc datuma. Jāizvēlās gan gadu, gan mēnesi: 
            </label>
            <?= Html::dropDownList('year', null, [
                2020 => "2020",
                2021 => "2021",
                2022 => "2022",
                2023 => "2023",
            ], ['prompt' => Yii::t('app', 'Choose year'), 'id' => 'invoices-year-selector']) ?>
            <?= Html::dropDownList('month', null, [
                "Janvāris",
                "Februāris",
                "Marts",
                "Aprīlis",
                "Maijs",
                "Jūnijs",
                "Jūlijs",
                "Augusts",
                "Septembris",
                "Oktobris",
                "Novembris",
                "Decembris"
            ], ['prompt' => Yii::t('app', 'Choose month'), 'id' => 'invoices-month-selector']) ?>
            <button class="btn btn-primary pull-right" id="export-sent-invoices">Eksportēt uz CSV (eksportētas tiks visas <strong>redzamās</strong> rindas</button>
                <?= GridView::widget([
                    'dataProvider' => $sentInvoices,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                            [
                            'attribute' => 'user name',
                            'value' => function($dataProvider){
                                if(!$dataProvider['student']) return;
                                
                                return $dataProvider['student']['first_name'] . ' ' . $dataProvider['student']['last_name'];
                            },
                            'label' => Yii::t('app', 'Student')
                        ],
                        'invoice_number',
                        'plan_name',
                        'plan_price',
                        'sent_date'
                    ],
                    'options' => [
                        'id' => 'sent-invoices-table',
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
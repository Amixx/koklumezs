<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;
use yii\helpers\Url;

$this->title = \Yii::t('app',  'Student evaluations');

?>
<div class="userlectureevaluations-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'lecture_id',
                'format' => 'raw',
                'value' => 'lecture.title',
                'filter' => Html::dropDownList(
                    'UserlectureevaluationsSearch[lecture_id]',
                    isset($get['UserlectureevaluationsSearch']['lecture_id'])
                        ? $get['UserlectureevaluationsSearch']['lecture_id']
                        : '',
                    $lectures,
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => 'student.email',
                'filter' => Html::dropDownList(
                    'UserlectureevaluationsSearch[user_id]',
                    isset($get['UserlectureevaluationsSearch']['user_id'])
                        ? $get['UserlectureevaluationsSearch']['user_id']
                        : '',
                    $students,
                    ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']
                ),
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
                    'view' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"> </span>',
                            $url,
                            ['title' => 'View', 'data-pjax' => '0']
                        );
                    },
                ],
                'urlCreator' => function ($action, $model) {
                    if ($action === 'view') {
                        return Url::base(true) . '/lekcijas/lekcija/' . $model['lecture_id'] . '?force=1';
                    }
                }
            ],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}']
        ],
    ]);
    ?>
</div>
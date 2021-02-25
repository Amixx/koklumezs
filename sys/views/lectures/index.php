<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;
use app\models\Lectures;

$this->title = \Yii::t('app', 'Lessons');

?>
<div class="lectures-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create lesson'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title',
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
                'attribute' => 'complexity',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->complexity;
                },
                'filter' => Html::dropDownList('TeacherLecturesSearch[complexity]', isset($get['TeacherLecturesSearch']['complexity']) ? $get['TeacherLecturesSearch']['complexity'] : '', Lectures::getComplexity(), ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => \Yii::t('app', 'Actions'),
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"> </span>',
                            $url,
                            ['title' => \Yii::t('app', 'View'), 'data-pjax' => '0', 'onclick' => "window.open('" . $url . "','newwindow','width=1200,height=1200');return false;"]
                        );
                    },
                    'update' => function ($url) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"> </span>',
                            $url,
                            ['title' => \Yii::t('app', 'Update'), 'data-pjax' => '0']
                        );
                    },
                    'delete' => function ($url) {
                        return Html::a(
                            '<span  class="glyphicon glyphicon glyphicon-trash"> </span>',
                            $url,
                            ['title' => \Yii::t('app', 'Delete'), 'data' => [
                                'confirm' => \Yii::t('app', 'Are you sure you want to delete this item?'),
                            ],]
                        );
                    }
                ],
                'urlCreator' => function ($action, $model) {
                    if ($action === 'view') {
                        $url = Url::base(true).'/lekcijas/lekcija/' . $model->id . '?force=1';
                        return $url;
                    }

                    if ($action === 'update') {
                        $url = Url::base(true).'/lectures/update/' . $model->id;
                        return $url;
                    }
                    if ($action === 'delete') {
                        $url = Url::base(true).'/lectures/delete/' . $model->id;
                        return $url;
                    }
                }
            ],

        ],
    ]); ?>


</div>
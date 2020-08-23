<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;
use app\models\Lectures;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'Lessons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lectures-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create lesson'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'title',
            // 'description:ntext',
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
                'attribute' => 'updated',
                'value' => 'updated',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated',
                    'language' => 'lv',
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],

            [
                'attribute' => 'author',
                'format' => 'raw',
                'value' => 'users.email',
                'filter' => Html::dropDownList('LecturesSearch[author]', isset($get['LecturesSearch']['author']) ? $get['LecturesSearch']['author'] : '', $admins, ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'complexity',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->complexity;
                },
                'filter' => Html::dropDownList('LecturesSearch[complexity]', isset($get['LecturesSearch']['complexity']) ? $get['LecturesSearch']['complexity'] : '', Lectures::getComplexity(), ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'attribute' => 'season',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->season;
                },
                'filter' => Html::dropDownList('LecturesSearch[season]', isset($get['LecturesSearch']['season']) ? $get['LecturesSearch']['season'] : '', Lectures::getSeasons(), ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => \Yii::t('app', 'Actions'),
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"> </span>',
                            $url,
                            ['title' => \Yii::t('app', 'View'), 'data-pjax' => '0', 'onclick' => "window.open('" . $url . "','newwindow','width=1200,height=1200');return false;"]
                        );
                    },
                    'update' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"> </span>',
                            $url,
                            ['title' => \Yii::t('app', 'Update'), 'data-pjax' => '0']
                        );
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a(
                            '<span  class="glyphicon glyphicon glyphicon-trash"> </span>',
                            $url,
                            ['title' => \Yii::t('app', 'Delete'), 'data' => [
                                'confirm' => \Yii::t('app', 'Are you sure you want to delete this item?'),
                            ],]
                        );
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = '/sys/lekcijas/lekcija/' . $model->id . '?force=1';
                        return $url;
                    }

                    if ($action === 'update') {
                        $url = '/sys/lectures/update/' . $model->id;
                        return $url;
                    }
                    if ($action === 'delete') {
                        $url = '/sys/lectures/delete/' . $model->id;
                        return $url;
                    }
                }
            ],

        ],
    ]); ?>


</div>
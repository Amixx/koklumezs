<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;

$this->title = \Yii::t('app', 'Exercises');
?>
<div class="lectures-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(\Yii::t('app', 'Create exercise'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => \Yii::t('app', 'Actions'),
                'template' => '{view} {update} {delete}',
            ],
            'name',
            [
                'attribute' => 'video',
                'value' => function ($dataProvider) {
                    if(!$dataProvider['video']) return '';
                    return Html::a(Yii::t('app', 'here'), $dataProvider['video'], ['target' => '_blank']);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'technique_video',
                'value' => function ($dataProvider) {
                    if(!$dataProvider['technique_video']) return '';
                    return Html::a(Yii::t('app', 'here'), $dataProvider['technique_video'], ['target' => '_blank']);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'exerciseTag',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return join(', ', array_map(function ($tag) {
                        return $tag['tag']['value'];
                    }, $dataProvider->exerciseTags));
                },
                'label' => Yii::t('app', 'Tags'),
                'filter' => Html::dropDownList(
                    'ExerciseSearch[exerciseTag]',
                    $get['ExerciseSearch']['exerciseTag'] ?? null,
                    \app\fitness\models\Tag::getForSelect(),
                    ['prompt' => '-- Visi --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'is_bodyweight',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    if(is_null($dataProvider['is_bodyweight'])) return Yii::t('app', 'Not set');
                    return $dataProvider['is_bodyweight']
                        ? Yii::t('app', 'Yes')
                        : Yii::t('app', 'No');
                },
                'label' => Yii::t('app', 'Is bodyweight'),
                'filter' => Html::dropDownList(
                    'ExerciseSearch[is_bodyweight]',
                    $get['ExerciseSearch']['is_bodyweight'] ?? null,
                    [
                        'not set' => Yii::t('app', 'Not set'),
                        true => Yii::t('app', 'Yes'),
                        false => Yii::t('app', 'No'),
                    ],
                    ['prompt' => '-- Visi --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'is_ready',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                   return $dataProvider['is_ready']
                       ? Yii::t('app', 'Yes')
                       : Yii::t('app', 'No');
                },
                'label' => Yii::t('app', 'Ready'),
                'filter' => Html::dropDownList(
                    'ExerciseSearch[is_ready]',
                    $get['ExerciseSearch']['is_ready'] ?? null,
                    [
                        true => Yii::t('app', 'Yes'),
                        false => Yii::t('app', 'No'),
                    ],
                    ['prompt' => '-- Visi --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'isAddedToAnyWorkouts',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return '';
                },
                'label' => Yii::t('app', 'Is in a workout'),
                'filter' => Html::dropDownList(
                    'ExerciseSearch[isAddedToAnyWorkouts]',
                    $get['ExerciseSearch']['isAddedToAnyWorkouts'] ?? null,
                    [
                        true => Yii::t('app', 'Yes'),
                        false => Yii::t('app', 'No'),
                    ],
                    ['prompt' => '-- Visi --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'isAddedToAnyProgressionChains',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return '';
                },
                'label' => Yii::t('app', 'Is in a progression chain'),
                'filter' => Html::dropDownList(
                    'ExerciseSearch[isAddedToAnyProgressionChains]',
                    $get['ExerciseSearch']['isAddedToAnyProgressionChains'] ?? null,
                    [
                        true => Yii::t('app', 'Yes'),
                        false => Yii::t('app', 'No'),
                    ],
                    ['prompt' => '-- Visi --', 'class' => 'form-control']
                ),
            ],
            [
                'attribute' => 'interchangeableExercisesCount',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return '';
                },
                'label' => Yii::t('app', 'Interchangeable exercises count'),
                'filter' => Html::dropDownList(
                    'ExerciseSearch[interchangeableExercisesCount]',
                    $get['ExerciseSearch']['interchangeableExercisesCount'] ?? null,
                    [
                            0 => 0,
                            1 => 1,
                            2 => 2,
                            3 => 3,
                            4 => 4,
                            '5+' => '5+',
                    ],
                    ['prompt' => '-- Visi --', 'class' => 'form-control']
                ),
            ],
        ],
    ]); ?>
</div>
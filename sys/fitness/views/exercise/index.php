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
            'name',
            [
                'attribute' => 'popularity_type',
                'value' => function ($dataProvider) {
                    return Yii::t('app',
                        $dataProvider['popularity_type'] === 'POPULAR'
                            ? 'Popular'
                            : ($dataProvider['popularity_type'] === 'AVERAGE' ? 'Average popularity' : 'Rare')
                    );
                },
                'filter' => Html::dropDownList(
                    'ExerciseSearch[popularity_type]',
                    $get['ExerciseSearch']['popularity_type'] ?? '',
                    [
                        'POPULAR' => Yii::t('app', 'Popular'),
                        'AVERAGE' => Yii::t('app', 'Average popularity'),
                        'RARE' => Yii::t('app', 'Rare')
                    ],
                    ['prompt' => '-- Visi --', 'class' => 'form-control']
                ),
            ],
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
                    if(!$dataProvider['video']) return '';
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
                    $get['ExerciseSearch']['exerciseTag'] ?? '',
                    \app\fitness\models\Tag::getForSelect(),
                    ['prompt' => '-- Visi --', 'class' => 'form-control']
                ),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => \Yii::t('app', 'Actions'),
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
</div>
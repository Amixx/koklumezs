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
                'value' => function ($dataProvider) {
                    $desc = $dataProvider['description'];
                    if (!$desc) return '';

                    $maxLength = 50;
                    if (strlen($desc) < $maxLength) return $desc;
                    $desc = $desc . " ";
                    $text = substr($desc, 0, $maxLength);
                    $desc = substr($text, 0, strrpos($desc, ' '));
                    $desc = $desc . "...";
                    return $desc;
                },
                'format' => 'raw',
            ],
            'technique_video',
            [
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return join(', ', array_map(function ($tag) {
                        return $tag['tag']['value'];
                    }, $dataProvider->exerciseTags));
                },
                'label' => Yii::t('app', 'Tags')
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => \Yii::t('app', 'Actions'),
                'template' => '{view} {update} {delete}',
            ],

        ],
    ]); ?>


</div>
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
            'first_set_video',
            'other_sets_video',
            'technique_video',
            [
                'attribute' => 'created_at',
                'value' => 'created_at',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'lv',
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
            ],
            [
                'attribute' => 'updated_at',
                'value' => 'updated_at',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'language' => 'lv',
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => \Yii::t('app', 'Actions'),
                'template' => '{update} {delete}',
            ],

        ],
    ]); ?>


</div>
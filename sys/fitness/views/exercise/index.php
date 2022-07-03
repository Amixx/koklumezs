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
            'description',
            'technique_video',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => \Yii::t('app', 'Actions'),
                'template' => '{view} {update} {delete}',
            ],

        ],
    ]); ?>


</div>
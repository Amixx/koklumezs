<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = \Yii::t('app',  'School evaluations');

?>
<div class="evaluations-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app',  'Create school evaluation'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title:ntext',
            'type:ntext',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
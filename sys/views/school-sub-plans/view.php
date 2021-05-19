<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

$this->title = $model->name;
['label' => \Yii::t('app', 'Subscription plans'), 'url' => ['index']];

\yii\web\YiiAsset::register($this);
?>
<div class="difficulties-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(\Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => \Yii::t('app', 'Do you really want to delete this entry?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description:html',
            [
                'attribute' => 'type',
                'value' => function ($dataProvider) {
                    return $dataProvider['type'] === 'lesson' ? 'Mācību' : 'Īres';
                }
            ],
            'months',
            'max_pause_weeks',
            'message',
            [
                'attribute' => 'Monthly cost',
                'label' => Yii::t('app',  'Monthly cost (with PVN)'),
                'value' => $planTotalCost,
            ]
        ],
    ]) ?>
    <h3><?= Yii::t('app', 'Plan files') ?></h3>
    <?= GridView::widget([
        'dataProvider' => $planFiles,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title',
            'file',
        ],
    ]); ?>
    <h3><?= Yii::t('app', 'Plan parts') ?></h3>
    <?= GridView::widget([
        'dataProvider' => $planParts,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'planpart.title',
            'planpart.monthly_cost',
        ],
    ]); ?>
</div>
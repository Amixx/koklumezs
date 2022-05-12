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
                    return $dataProvider->typeText();
                }
            ],
            'months',
            'max_pause_weeks',
            'message',
            'days_for_payment',
            [
                'attribute' => 'recommend_after_trial',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider->recommend_after_trial ?  'Yes' : 'No');
                }
            ],
            [
                'attribute' => 'allow_single_payment',
                'value' => function ($dataProvider) {
                    return Yii::t('app', $dataProvider->allow_single_payment ?  'Yes' : 'No');
                }
            ],
            [
                'attribute' => 'Monthly cost',
                'label' => Yii::t('app',  'Monthly cost (with PVN)'),
                'value' => $planTotalCost,
            ],
            'stripe_single_price_id',
            'stripe_recurring_price_id',
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
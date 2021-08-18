<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SchoolSubplanParts;

$this->title = \Yii::t('app', 'Subscription plans');

?>
<div class="subplans-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create subscription plan'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'description:html',
            [
                'attribute' => 'type',
                'value' => function ($dataProvider) {
                    return $dataProvider->typeText();
                }
            ],
            'pvn_percent',
            [
                'attribute' => 'Monthly cost',
                'label' => Yii::t('app',  'Monthly cost (with PVN)'),
                'value' => function ($model) {
                    return SchoolSubplanParts::getPlanTotalCost($model->id);
                },
            ],
            'months',
            'max_pause_weeks',
            'message',
            'days_for_payment',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
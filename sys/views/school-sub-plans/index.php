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
            'stripe_single_price_id',
            'stripe_recurring_price_id',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
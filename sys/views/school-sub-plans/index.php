<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = \Yii::t('app', 'Subscription plans');
$this->params['breadcrumbs'][] = $this->title;
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
            'pvn_percent',
            'monthly_cost',
            'months',
            'max_pause_weeks',
            'message',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
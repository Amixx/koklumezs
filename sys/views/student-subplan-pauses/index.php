<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = \Yii::t('app', 'Plan pauses');

?>
<div>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create plan pause'), ['teacher-create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'studentPlan.user.first_name',
            'studentPlan.user.last_name',
            'studentPlan.plan.name',
            'weeks',
            'start_date',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',                      
            ],
        ],
    ]); ?>
</div>
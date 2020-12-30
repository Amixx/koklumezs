<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = \Yii::t('app', 'Subscription plans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subplans-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="plans-tab" data-toggle="tab" href="#plans" role="tab" aria-controls="plans" aria-selected="true"><?= \Yii::t('app', 'School subscription plans') ?></a>
        </li>
         <li class="nav-item">
            <a class="nav-link" id="pauses-tab" data-toggle="tab" href="#pauses" role="tab" aria-controls="pauses" aria-selected="false"><?= \Yii::t('app', 'Plan pauses') ?></a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="plans" role="tabpanel" aria-labelledby="plans-tab">
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
        <div class="tab-pane fade" id="pauses" role="tabpanel" aria-labelledby="pauses-tab">
            <p>
                <?= Html::a(\Yii::t('app', 'Create plan pause'), ['student-subplan-pauses/teacher-create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $pausesDataProvider,
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
                        'urlCreator' => function ($action, $model, $key, $index) {
                            return Url::base(true)."/student-subplan-pauses/$action?id=" . $model->id;
                        }                     
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
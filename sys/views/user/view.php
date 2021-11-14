<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\SchoolSubplanParts;


$this->title = $model->id;
['label' => \Yii::t('app',  'Users'), 'url' => ['index']];
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app',  'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(\Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => \Yii::t('app',  'Do you really want to delete this entry?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                <?= \Yii::t('app', 'Student') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="plan-tab" data-toggle="tab" href="#plan" role="tab" aria-controls="plan" aria-selected="false">
                <?= \Yii::t('app', 'Student\'s subscription plans') ?>
            </a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?= DetailView::widget([
                'model' => $model,
                'id' => 'student-info-details',
                'attributes' => [
                    'id',
                    'first_name',
                    'last_name',
                    'phone_number',
                    'email:email',
                    'user_level',
                    [
                        'attribute' => 'about',
                    ],
                ],
            ]) ?>
        </div>
        <div class="tab-pane fade" id="plan" role="tabpanel" aria-labelledby="plan-tab">
            <?php if ($studentSubPlans == null) { ?>
                <h3><?= Yii::t('app', 'User has no subscription plans') ?>!</h3>
                <p><?= Html::a(
                        \Yii::t('app',  'You can give the student a plan in the edit page') . '!',
                        ['update', 'id' => $model->id]
                    ) ?></p>
            <?php } else {
                echo GridView::widget([
                    'dataProvider' => $studentSubPlans,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'start_date',
                        'sent_invoices_count',
                        'times_paid',
                        [
                            'label' => Yii::t('app', 'Plan name'),
                            'value' => function ($dataProvider) {
                                return $dataProvider->plan ? $dataProvider->plan->name : "(Dzēsts plāns)";
                            }
                        ],
                        [
                            'label' => Yii::t('app', 'Plan type'),
                            'value' => function ($dataProvider) {
                                return $dataProvider->plan ? $dataProvider->plan->typeText() : "-";
                            }
                        ],
                        [
                            'label' => Yii::t('app', 'Plan monthly cost'),
                            'value' => function ($dataProvider) {
                                return $dataProvider->plan
                                    ? SchoolSubplanParts::getPlanTotalCost($dataProvider->plan['id'])
                                    : "-";
                            }
                        ],
                        [
                            'label' => Yii::t('app', 'Plan end date'),
                            'value' => function ($dataProvider) use ($planEndDates) {
                                foreach ($planEndDates as $planEndDate) {
                                    if ($planEndDate['planId'] == $dataProvider->id) {
                                        return $planEndDate['endDate'];
                                    }
                                }
                                return NULL;
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{delete}',
                            'urlCreator' => function ($action, $model) {
                                if ($action === 'delete') {
                                    return Url::base(true) . '/student-sub-plans/delete?id=' . $model->id;
                                }
                            },
                        ],
                    ],
                ]);
            } ?>
        </div>
    </div>
</div>
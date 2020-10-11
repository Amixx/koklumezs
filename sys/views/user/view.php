<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?= \Yii::t('app', 'Student') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="plan-tab" data-toggle="tab" href="#plan" role="tab" aria-controls="plan" aria-selected="false"><?= \Yii::t('app', 'Student\'s subscription plan') ?></a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'first_name',
                    'last_name',
                    'phone_number',
                    'email:email',
                    'username',
                    'user_level',
                    'about'
                ],
            ]) ?>
        </div>
        <div class="tab-pane fade" id="plan" role="tabpanel" aria-labelledby="plan-tab">
            <?php if ($studentSubPlan == null) { ?>
                <h3><?= Yii::t('app', 'User has no subscription plan') ?>!</h3>
                <p><?= Html::a(\Yii::t('app',  'You can give the student a plan in the edit page') . '!', ['update', 'id' => $model->id]) ?></p>
            <?php } else {
                echo DetailView::widget([
                    'model' => $studentSubPlan,
                    'attributes' => [
                        'start_date',
                        'sent_invoices_count',
                        'times_paid',
                        [
                            'label' => Yii::t('app', 'Plan name'),
                            'value' => $studentSubPlan->plan->name,
                        ],
                        [
                            'label' => Yii::t('app', 'Plan monthly cost'),
                            'value' => $studentSubPlan->plan->monthly_cost,
                        ],
                        [
                            'label' => Yii::t('app', 'Plan discount (percentage)'),
                            'value' => $studentSubPlan->plan->discount,
                        ],
                        [
                            'label' => Yii::t('app', 'Plan months count'),
                            'value' => $studentSubPlan->plan->months,
                        ],
                    ],
                ]);
            } ?>
        </div>
    </div>



</div>
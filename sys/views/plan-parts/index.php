<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app', 'Plan parts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="difficulties-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="container">
        <div class="row">
            <div class="col-12">
                 <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'title',
                        'monthly_cost',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{delete} {update}'
                        ],
                    ],
                ]); ?>
            </div>
        </div>
        <hr>
        <div class="row">
            <h3><?= Yii::t('app', 'Create a plan part') ?></h3>
            <div class="col-12">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'title')->textInput() ?>
                <?= $form->field($model, 'monthly_cost')->textInput() ?>

                <div class="form-group">
                    <?= Html::submitButton(\Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
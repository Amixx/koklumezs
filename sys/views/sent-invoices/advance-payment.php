<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  yii\jui\DatePicker;

$this->title = \Yii::t('app', 'Register payment');
?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>

    <div>
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'plan_id')->dropDownList($studentSubPlans, ['prompt' => '']) ?>

        <?= $form->field($model, 'paid_months_count')->textInput(['type' => 'number'])->label(Yii::t('app', 'Paid months')) ?>
        <?= $form->field($model, 'paid_date')
            ->widget(DatePicker::class, ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv'])
            ->label(Yii::t('app', 'Date of payment:')) ?>
        <?= Html::submitButton(\Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>
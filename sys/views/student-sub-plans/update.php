<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  yii\jui\DatePicker;

$this->title = \Yii::t('app', 'Edit student subscription plan');

\Yii::t('app', 'Edit');
?>
<div class="difficulties-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div>
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'start_date')->textInput()
            ->widget(DatePicker::class, ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv']) ?>

        <?= $form->field($model, 'sent_invoices_count')->textInput() ?>
        <?= $form->field($model, 'times_paid')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  yii\jui\DatePicker;

?>

<div>

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($realInvoice == null) { ?>
        <div class="form-group">
            <?= $form->field($model, 'paid_date')->widget(DatePicker::class, ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv'])->label(Yii::t('app', 'Date of payment:')) ?>
            <?= Html::submitButton(\Yii::t('app', 'Mark as paid'), ['class' => 'btn btn-success']) ?>
        </div>
    <?php } else { ?>
        <div class="form-group">
            <h4><?= Yii::t('app', 'This invoice has already been paid!') ?></h4>
        </div>
    <?php } ?>


    <hr>

    <div class="form-group">
        <?= Html::a(
            \Yii::t('app', 'Delete advance invoice'),
            ['/sent-invoices/delete', 'id' => $model->id],
            [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => \Yii::t('app', 'Do you really want to delete this entry?')],
            ]
        ) ?>

        <?php if ($realInvoice != null)
            echo Html::a(
                \Yii::t('app', 'Delete real invoice'),
                ['/sent-invoices/delete', 'id' => $realInvoice->id],
                [
                    'class' => 'btn btn-danger',
                    'data' => ['confirm' => \Yii::t('app', 'Do you really want to delete this entry?')],
                ]
            ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
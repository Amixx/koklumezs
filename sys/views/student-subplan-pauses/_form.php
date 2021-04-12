<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  yii\jui\DatePicker;

?>

<div>
    <?php $form = ActiveForm::begin(); ?>

    <?= Html::dropDownList('user_id', $userId ? $userId : null, $users, [
        'prompt' => Yii::t('app', 'Choose a student'),
        'options' => [$userId => ['selected' => 'selected']]
    ]) ?>

    <?= $form->field($model, 'weeks')->textInput() ?>

    <?= $form->field($model, 'start_date')->textInput()
        ->widget(DatePicker::class, ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv']) ?>

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
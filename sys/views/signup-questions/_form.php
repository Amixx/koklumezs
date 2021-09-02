<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="difficulties-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'text')->textInput() ?>

    <?= $form->field($model, 'multiple_choice')->dropDownList(
        [false => \Yii::t('app',  'No'), true => \Yii::t('app', 'Yes')],
        ['prompt' => '', 'class' => 'small-dropdown']
    ) ?>
    <?= $form->field($model, 'allow_custom_answer')->dropDownList(
        [false => \Yii::t('app',  'No'), true => \Yii::t('app', 'Yes')],
        ['prompt' => '', 'class' => 'small-dropdown']
    ) ?>

    <h4>Atbilžu varianti (neobligāti)</h4>
    <div id="signup-questions-answer-choices">
        <?php for ($i = 0; $i < 3; $i++) { ?>
            <div class="form-group">
                <?= Html::input('text', 'answer_choice[' . $i . ']', '', ['class' => 'form-control']) ?>
            </div>
        <?php } ?>
    </div>

    <button class="btn btn-primary" id="signup-questions-add-answer">Pievienot vēl vienu atbilžu variantu</button>
    <hr>
    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
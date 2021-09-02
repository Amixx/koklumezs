<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app',  'A few questions before you begin');

?>

<div class="login-box">
    <div class="rent-form row">
        <?php $form = ActiveForm::begin(['id' => 'rent-form', 'enableClientValidation' => false]); ?>

        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <h4><?= $question['text'] ?></h4>
            <?php if ($question['multiple_choice'] && !empty($question['answerChoices'])) { ?>
                <?php $x = [];
                foreach ($question['answerChoices'] as $choice) {
                    $x[$choice['text']] = $choice['text'];
                } ?>

                <?= $form->field($model, 'answer')->radioList($x) ?>

                <?php if ($question['allow_custom_answer']) { ?>
                    <?= Html::activeRadio($model, 'custom_answer_selected') ?>
                    <?= Html::activeTextArea($model, 'custom_answer', ['class' => 'signup-question-answer']); ?>
                <?php } ?>
            <?php } else { ?>
                <?= Html::activeTextArea($model, 'answer', ['class' => 'signup-question-answer']); ?>
            <?php } ?>
        </div>
        <div class="col-sm-3"></div>

        <div class="col-sm-12 text-center">
            <div>
                <?= Html::submitButton(
                    \Yii::t('app',  'Continue'),
                    ['class' => 'btn btn-primary btn-flat', 'name' => 'login-button']
                ) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
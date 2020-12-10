<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app',  'A few questions before you begin');

?>

<div class="login-box">
    <div class="rent-or-buy-form row">
        <?php $form = ActiveForm::begin(['id' => 'rent-or-buy-form', 'enableClientValidation' => false]); ?>

        <?php foreach($questions as $q) { ?>
            <div class="col-12">
                <h4><?= $q['text'] ?></h4>
                <?= Html::textarea("answers[" . $q['id'] . "]", '', ['class' => 'signup-question-answer']) ?>
            </div>
        <?php } ?>

        <div class="col-sm-12 text-center">
            <div>
                <?= Html::submitButton(\Yii::t('app',  'Submit'), ['class' => 'btn btn-primary btn-flat', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
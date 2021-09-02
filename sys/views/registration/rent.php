<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app',  'Your first kokle');

?>

<div class="login-box">
    <div class="rent-form row">

        <div class="container">
            <h2 class="text-center"><?= Yii::t('app', 'Rent kokle') ?></h2>
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <?php if ($text) { ?>
                        <div style="font-size:16px"><?= $text ?></div>
                    <?php } ?>
                    <?php $form = ActiveForm::begin(['id' => 'rent-form', 'enableClientValidation' => false]); ?>

                    <?= Html::a(Yii::t('app', 'View rent agreement'), Url::to(["documents/Kokles līgums.docx"]), ['class' => 'btn btn-primary']) ?>

                    <div style='margin-top: 16px;'>
                        <?= $form->field($model, 'agreeToTerms')->checkBox()
                            ->label(\Yii::t('app', 'I have read and agree to the terms of the lease agreement')); ?>
                    </div>

                    <div class="col-sm-12 text-center">
                        <div>
                            <?= Html::a(Yii::t('app', 'Back'), $backUrl, [
                                'class' => 'btn btn-info'
                            ]) ?>
                            <?= Html::submitButton(\Yii::t('app', 'Continue'), [
                                'class' => 'btn btn-orange btn-flat rent-submit-button',
                                'name' => 'login-button'
                            ]) ?>

                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>
</div>
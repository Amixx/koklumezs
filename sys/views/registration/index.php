<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app',  'Let\'s get acquainted') . "!";

function getFieldOptions($fieldName, $hasInfo = false)
{
    $glyphon = 'user';
    $classes = 'form-group has-feedback';

    switch ($fieldName) {
        case 'password':
            $glyphon = 'lock';
            break;
        case 'email':
            $glyphon = 'envelope';
            break;
        case 'phone_number':
            $glyphon = 'earphone';
            break;
        default:
            break;
    }
    if ($hasInfo) {
        $classes .= ' field-with-info-widget';
    }

    return [
        'options' => ['class' => $classes],
        'inputTemplate' => "{input}<span class='glyphicon glyphicon-" . $glyphon . " form-control-feedback'></span>"
    ];
}
?>

<div class="login-box">
    <div class="reg-page-1">
        <h3 style=""><?= $this->title ?></h3>
        <div class="reg-page-1-container">
            <div class="reg-page-1-img-wrapper">
                <img src="<?= $registration_image ?>">
            </div>
            <div class="reg-page-1-title-outer">
                <div class="reg-page-1-title-inner"><?= $registration_title ?></div>
            </div>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <div class="row" style="margin-top: 32px;">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <?= $form
                    ->field($model, 'first_name', getFieldOptions('first_name'))
                    ->label(false)
                    ->textInput(['placeholder' => Yii::t('app', 'Name')]) ?>
                <div class="reg-page-1-btn-container">
                    <?= Html::submitButton(
                        \Yii::t('app',  'Continue'),
                        ['class' => 'btn btn-orange btn-block btn-flat', 'name' => 'login-button', 'id' => 'registration-button']
                    ) ?>
                    <span>1 no 4</span>
                </div>
            </div>
            <div class="col-sm-4"></div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
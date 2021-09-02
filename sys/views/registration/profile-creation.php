<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = strtoupper(\Yii::t('app',  'Let\'s create your profile') . "!");

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

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <div class="signup-form-section">
            <?= $form
                ->field($model, 'first_name', getFieldOptions('first_name'))
                ->label(false)
                ->textInput(['placeholder' => Yii::t('app', 'Name')]) ?>

            <?= $form
                ->field($model, 'last_name', getFieldOptions('last_name'))
                ->label(false)
                ->textInput(['placeholder' => Yii::t('app', 'Surname')]) ?>
        </div>

        <div class="signup-form-section">
            <?= $form
                ->field($model, 'email', getFieldOptions('email'))
                ->label(false)
                ->textInput(['placeholder' => Yii::t('app', 'E-mail')]) ?>

            <?= $form
                ->field($model, 'password', getFieldOptions('password'))
                ->label(false)
                ->passwordInput(['placeholder' => Yii::t('app', 'Create password')]) ?>
            <?= $form
                ->field($model, 'passwordRepeat', getFieldOptions('password'))
                ->label(false)
                ->passwordInput(['placeholder' => Yii::t('app', 'Repeat password')]) ?>
        </div>

        <div style='margin-top: 16px;'>
            <?= $form->field($model, 'agree')->checkBox()
                ->label(\Yii::t('app', 'I agree to receive emails regarding information about lectures, the education process, and events')); ?>
        </div>

        <div class="col-sm-4"></div>
        <div class="col-sm-4">
            <?= Html::submitButton(
                \Yii::t('app',  'Register'),
                ['class' => 'btn btn-orange btn-block btn-flat', 'name' => 'login-button', 'id' => 'registration-button']
            ) ?>
        </div>
        <div class="col-sm-4"></div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
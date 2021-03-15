<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Users;
use  yii\jui\DatePicker;

$isTeacher = Users::isCurrentUserTeacher();
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['enableClientValidation'=>false]); ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?= \Yii::t('app',  'User data') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="params-tab" data-toggle="tab" href="#params" role="tab" aria-controls="params" aria-selected="false"><?= \Yii::t('app',  'Parameters') ?></a>
        </li>
        <?php if ($model->id) { ?>
            <li class="nav-item">
                <a class="nav-link" id="plan-tab" data-toggle="tab" href="#plan" role="tab" aria-controls="plan" aria-selected="false"><?= \Yii::t('app', 'Student\'s subscription plan') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="payer-tab" data-toggle="tab" href="#payer" role="tab" aria-controls="payer" aria-selected="false"><?= \Yii::t('app', 'Payer') ?></a>
            </li>
        <?php } ?>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'value' => ""]) ?>

            <?php if (!$isTeacher) {
                echo $form->field($model, 'user_level')->dropDownList(['Admin' => \Yii::t('app',  'Administrator'), 'Student' => \Yii::t('app',  'Student'), 'Teacher' => \Yii::t('app',  'Teacher')], ['prompt' => '']);
            ?>
                <label for="teacher_instrument"><?= \Yii::t('app',  'Instrument (only for teachers)') ?>:</label>
            <?php
                echo Html::input("text", "teacher_instrument", "", ['class' => 'form-control']);
            }; ?>

            <?= $form->field($model, 'language')->dropDownList(['lv' => \Yii::t('app',  'Latvian'), 'eng' => \Yii::t('app',  'English')], ['prompt' => '']) ?>

            <?= $form->field($model, 'subscription_type')->dropDownList(['free' => \Yii::t('app',  'For free'), 'paid' => \Yii::t('app',  'Paid'), 'lead' => \Yii::t('app',  'Lead'),], ['prompt' => '']) ?>

            <?= $form->field($model, 'status')->dropDownList([Users::STATUS_INACTIVE => \Yii::t('app',  'Inactive'), Users::STATUS_ACTIVE => \Yii::t('app',  'Active'), Users::STATUS_PASSIVE => \Yii::t('app',  'Passive')], ['prompt' => '']) ?>

            <?= $form->field($model, 'about')->textArea(['rows' => 6]) ?>

            <?= $form->field($model, 'dont_bother')->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv']) ?>

            <?= $form->field($model, 'allowed_to_download_files')->dropDownList([0 => \Yii::t('app',  'No'), 1 => \Yii::t('app',  'Yes')], ['prompt' => '', 'value' => $model['allowed_to_download_files'] == true ? 1 : 0]) ?>
        </div>
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <?php if ($difficulties) { ?>
                <?= $this->render('difficulties', ['difficulties' => $difficulties, 'studentGoals' => $studentGoals]) ?>
            <?php } ?>
        </div>
        <div class="tab-pane fade" id="plan" role="tabpanel" aria-labelledby="plan-tab">
            <div class="form-group">
                <?php if (isset($schoolSubPlans) && $schoolSubPlans) { ?>
                    <?= $form->field($studentSubplan, 'plan_id')->dropDownList($schoolSubPlans, ['prompt' => ''])->label(Yii::t('app', 'Subscription plan')) ?>
                    <?= $form->field($studentSubplan, 'start_date')->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv'])->label(Yii::t('app', 'Start date')) ?>
                    <?= $form->field($studentSubplan, 'sent_invoices_count')->textInput(['type' => 'number'])->label(Yii::t('app', 'Sent invoices count')) ?>
                    <?= $form->field($studentSubplan, 'times_paid')->textInput(['type' => 'number'])->label(Yii::t('app', 'Times paid')) ?>
                <?php } ?>
            </div>
            <div class="form-group">
                <?= Html::a(\Yii::t('app',  'Remove subscription plan'), ["/student-sub-plans/delete?userId=".$model["id"]], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
        <div class="tab-pane fade" id="payer" role="tabpanel" aria-labelledby="payer-tab">
            <h4><?= Yii::t('app', 'The payer\'s information will be used if at least the name and address are filled in') ?>.</h4>
            <div class="form-group">
                <?= $form->field($model, 'payer[name]')->textInput()->label(Yii::t('app', 'Name/Title')) ?>
                <?= $form->field($model, 'payer[personal_code]')->textInput()->label(Yii::t('app', 'Personal code')) ?>
                <?= $form->field($model, 'payer[address]')->textInput()->label(Yii::t('app', 'Legal address')) ?>
                <?= $form->field($model, 'payer[registration_number]')->textInput()->label(Yii::t('app', 'Registration number')) ?>
                <?= $form->field($model, 'payer[pvn_registration_number]')->textInput()->label(Yii::t('app', 'PVN registration number')) ?>
                <?= $form->field($model, 'payer[bank]')->textInput()->label(Yii::t('app', 'Bank')) ?>
                <?= $form->field($model, 'payer[swift]')->textInput()->label('SWIFT') ?>
                <?= $form->field($model, 'payer[account_number]')->textInput()->label(Yii::t('app', 'Account number')) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
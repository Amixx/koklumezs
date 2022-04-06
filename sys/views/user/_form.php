<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Users;
use  yii\jui\DatePicker;
use yii\grid\GridView;
use app\models\SchoolSubplanParts;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

$userContext = Yii::$app->user->identity;
$isTeacher = $userContext->isTeacher();

$ckeditorOptions = ElFinder::ckeditorOptions(
    'elfinder',
    [
        'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
        'inline' => false, //по умолчанию false
        'filter' => ['image', 'application/pdf', 'text', 'video'],    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    ]
);

?>

<div class="user-form">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                <?= Yii::t('app',  'User data') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="params-tab" data-toggle="tab" href="#params" role="tab" aria-controls="params" aria-selected="false">
                <?= Yii::t('app',  'Parameters') ?>
            </a>
        </li>
        <?php if ($model->id) { ?>
            <li class="nav-item">
                <a class="nav-link" id="plan-tab" data-toggle="tab" href="#plan" role="tab" aria-controls="plan" aria-selected="false">
                    <?= Yii::t('app', 'Add subscription plan to student') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="payer-tab" data-toggle="tab" href="#payer" role="tab" aria-controls="payer" aria-selected="false">
                    <?= Yii::t('app', 'Payer') ?>
                </a>
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
                echo $form->field($model, 'user_level')->dropDownList([
                    'Admin' => Yii::t('app',  'Administrator'),
                    'Student' => Yii::t('app',  'Student'),
                    'Teacher' => Yii::t('app',  'Teacher')
                ], ['prompt' => '']);
            ?>
                <label for="teacher_instrument"><?= Yii::t('app',  'Instrument (only for teachers)') ?>:</label>
            <?php
                echo Html::input("text", "teacher_instrument", "", ['class' => 'form-control']);
            } ?>

            <?= $form->field($model, 'language')->dropDownList([
                'lv' => Yii::t('app',  'Latvian'),
                'eng' => Yii::t('app',  'English')
            ], ['prompt' => '']) ?>

            <?= $form->field($model, 'subscription_type')->dropDownList([
                'free' => Yii::t('app',  'For free'),
                'paid' => Yii::t('app',  'Paid'),
                'lead' => Yii::t('app',  'Lead'),
            ], ['prompt' => '']) ?>

            <?= $form->field($model, 'status')->dropDownList([
                Users::STATUS_INACTIVE => Yii::t('app',  'Inactive'),
                Users::STATUS_ACTIVE => Yii::t('app',  'Active'),
                Users::STATUS_PASSIVE => Yii::t('app',  'Passive')
            ], ['prompt' => '']) ?>

            <?= $form->field($model, 'about')->widget(CKEditor::class, [
                'editorOptions' => $ckeditorOptions,
            ]) ?>

            <?= $form->field($model, 'allowed_to_download_files')->dropDownList([
                0 => Yii::t('app',  'No'),
                1 => Yii::t('app',  'Yes')
            ], [
                'prompt' => '',
                'value' => $model['allowed_to_download_files'] ? 1 : 0
            ]) ?>

            <?= $form->field($model, 'is_test_user')->dropDownList([
                0 => Yii::t('app',  'No'),
                1 => Yii::t('app',  'Yes')
            ], [
                'prompt' => '',
                'value' => $model['is_test_user'] ? 1 : 0
            ]) ?>
        </div>
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <?php if ($difficulties) { ?>
                <?= $this->render('difficulties', ['difficulties' => $difficulties, 'studentGoals' => $studentGoals]) ?>
            <?php } ?>
        </div>
        <div class="tab-pane fade" id="plan" role="tabpanel" aria-labelledby="plan-tab">
            <div class="form-group">
                <?php if (isset($studentSubplans)) { ?>
                    <?= GridView::widget([
                        'dataProvider' => $studentSubplans,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'start_date',
                            'sent_invoices_count',
                            'times_paid',
                            [
                                'label' => Yii::t('app', 'Plan name'),
                                'value' => function ($dataProvider) {
                                    return $dataProvider->plan ? $dataProvider->plan->name : "(Dzēsts plāns)";
                                }
                            ],
                            [
                                'label' => Yii::t('app', 'Plan monthly cost'),
                                'value' => function ($dataProvider) {
                                    return $dataProvider->plan
                                        ? SchoolSubplanParts::getPlanTotalCost($dataProvider->plan['id'])
                                        : "-";
                                }
                            ],
                            [
                                'label' => Yii::t('app', 'Plan months count'),
                                'value' => function ($dataProvider) {
                                    return $dataProvider->plan ? $dataProvider->plan->months : "-";
                                }
                            ],
                            [
                                'label' => Yii::t('app', 'Type'),
                                'value' => function ($dataProvider) {
                                    return $dataProvider->plan ? $dataProvider->plan->typeText() : "-";
                                }
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {delete}',
                                'urlCreator' => function ($action, $model) {
                                    if ($action === 'update') {
                                        return Url::base(true) . '/student-sub-plans/update?id=' . $model->id;
                                    }
                                    if ($action === 'delete') {
                                        return Url::base(true) . '/student-sub-plans/delete?id=' . $model->id;
                                    }
                                },
                            ],
                        ],
                    ]); ?>

                <?php } ?>
            </div>
            <div class="form-group">
                <?php if (isset($schoolSubPlans) && $schoolSubPlans) { ?>
                    <?= $form->field($studentSubplanModel, 'plan_id')
                        ->dropDownList($schoolSubPlans, ['prompt' => ''])
                        ->label(Yii::t('app', 'Subscription plan')) ?>
                    <?= $form->field($studentSubplanModel, 'start_date')
                        ->widget(DatePicker::class, ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv'])
                        ->label(Yii::t('app', 'Start date')) ?>
                    <?= $form->field($studentSubplanModel, 'sent_invoices_count')
                        ->textInput(['type' => 'number'])
                        ->label(Yii::t('app', 'Sent invoices count')) ?>
                    <?= $form->field($studentSubplanModel, 'times_paid')
                        ->textInput(['type' => 'number'])
                        ->label(Yii::t('app', 'Times paid')) ?>
                <?php } ?>
            </div>
            <!-- <div class="form-group">
                <?= Html::a(
                    Yii::t('app', 'Remove subscription plan'),
                    ["/student-sub-plans/delete?userId=" . $model["id"]],
                    ['class' => 'btn btn-danger']
                ) ?>
            </div> -->
        </div>
        <div class="tab-pane fade" id="payer" role="tabpanel" aria-labelledby="payer-tab">
            <?php
            if($model['payer'] && $model['payer']['should_use']) {
                $statusText = 'The payer\'s information is being used';
                $statusColor = 'green';
            } else {
                $statusText = 'The payer\'s information is not being used';
                $statusColor = 'red';
            }
        
            ?>
            <h4 style="color:<?= $statusColor ?>"><?= Yii::t('app', $statusText) ?>.</h4>
            <div class="form-group">
                <?= $form->field($model, 'payer[should_use]')->dropDownList([
                    '0' => Yii::t('app',  'No'),
                    '1' => Yii::t('app',  'Yes')
                ], ['prompt' => ''])->label(Yii::t('app', 'Should use payer information')) ?>
                <?= $form->field($model, 'payer[name]')->textInput()
                    ->label(Yii::t('app', 'Name/Title')) ?>
                <?= $form->field($model, 'payer[email]')->textInput()->label(Yii::t('app', 'E-mail'))  ?>
                <?= $form->field($model, 'payer[personal_code]')->textInput()
                    ->label(Yii::t('app', 'Personal code')) ?>
                <?= $form->field($model, 'payer[address]')->textInput()->label(Yii::t('app', 'Legal address')) ?>
                <?= $form->field($model, 'payer[registration_number]')->textInput()
                    ->label(Yii::t('app', 'Registration number')) ?>
                <?= $form->field($model, 'payer[pvn_registration_number]')->textInput()
                    ->label(Yii::t('app', 'PVN registration number')) ?>
                <?= $form->field($model, 'payer[bank]')->textInput()
                    ->label(Yii::t('app', 'Bank')) ?>
                <?= $form->field($model, 'payer[swift]')->textInput()
                    ->label('SWIFT') ?>
                <?= $form->field($model, 'payer[account_number]')->textInput()
                    ->label(Yii::t('app', 'Account number')) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
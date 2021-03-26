<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use mihaildev\elfinder\InputFile;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */

$ckeditorOptions = ElFinder::ckeditorOptions(
    'elfinder',
    [
        'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
        'inline' => false, //по умолчанию false
        'filter' => ['image', 'application/pdf', 'text', 'video'],    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    ]
);

$this->title = \Yii::t('app',  'Edit school settings') . ': ';
\Yii::t('app',  'Edit');
?>
<div class="school-settings-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="school-settings-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'background_image')->widget(InputFile::className(), [
            'language' => 'lv',
            'controller' => 'elfinder',
            'filter' => ['image'],
            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            'options' => ['class' => 'form-control'],
            'buttonOptions' => ['class' => 'btn btn-default'],
            'multiple' => false,
        ]); ?>
        <?= $form->field($model, 'registration_background_image')->widget(InputFile::className(), [
            'language' => 'lv',
            'controller' => 'elfinder',
            'filter' => ['image'],
            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            'options' => ['class' => 'form-control'],
            'buttonOptions' => ['class' => 'btn btn-default'],
            'multiple' => false,
        ]); ?>
        <?= $form->field($model, 'video_thumbnail')->widget(InputFile::className(), [
            'language' => 'lv',
            'controller' => 'elfinder',
            'filter' => ['image'],
            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            'options' => ['class' => 'form-control'],
            'buttonOptions' => ['class' => 'btn btn-default'],
            'multiple' => false,
        ]); ?>
        <?= $form->field($model, 'logo')->widget(InputFile::className(), [
            'language' => 'lv',
            'controller' => 'elfinder',
            'filter' => ['image'],
            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            'options' => ['class' => 'form-control'],
            'buttonOptions' => ['class' => 'btn btn-default'],
            'multiple' => false,
        ]); ?>
        <?= $form->field($model, 'difficulties_color')->textInput(['class' => 'form-control form-group has-feedback field-with-info-widget']) ?>
        <span class="glyphicon glyphicon-info-sign info info-school-difficulties_color" style="margin-top: -50px;"></span>

        <?= $form->field($model, 'email')->textInput(['class' => 'form-control form-group has-feedback field-with-info-widget']) ?>
        <span class="glyphicon glyphicon-info-sign info info-school-email" style="margin-top: -50px;"></span>

        <?= $form->field($model, 'registration_message')->widget(CKEditor::className(), [
            'editorOptions' => $ckeditorOptions,
        ]) ?>

        <?= $form->field($model, 'registration_title')->widget(CKEditor::className(), [
            'editorOptions' => $ckeditorOptions,
        ]) ?>
        <?= $form->field($model, 'login_title')->widget(CKEditor::className(), [
            'editorOptions' => $ckeditorOptions,
        ]) ?>

        <?= $form->field($model, 'renter_message')->widget(CKEditor::className(), [
            'editorOptions' => $ckeditorOptions,
        ]) ?>

        <?= $form->field($model, 'rent_schoolsubplan_id')
            ->dropDownList(
                $schoolSubPlans,
                ['prompt' => '']
            )->label(Yii::t('app', 'Subscription plan')) ?>

        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
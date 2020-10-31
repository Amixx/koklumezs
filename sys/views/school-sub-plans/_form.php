<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\elfinder\InputFile;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

$ckeditorOptions = ElFinder::ckeditorOptions(
    'elfinder',
    [
        'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
        'inline' => false, //по умолчанию false
        'filter' => ['image', 'application/pdf', 'text', 'video'],    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    ]
);
?>

<div class="difficulties-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'description')->widget(CKEditor::className(), [
        'editorOptions' => $ckeditorOptions,
    ]) ?>
    <?= $form->field($model, 'monthly_cost')->textInput() ?>
    <?= $form->field($model, 'months')->textInput() ?>
    <?= $form->field($model, 'file')->widget(InputFile::className(), [
        'language' => 'lv',
        'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
        'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
        'options' => ['class' => 'form-control'],
        'buttonOptions' => ['class' => 'btn btn-default'],
        'multiple' => true, // возможность выбора нескольких файлов
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
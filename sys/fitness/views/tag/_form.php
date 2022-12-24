<?php

use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use mihaildev\elfinder\InputFile;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$ckeditorOptions = ElFinder::ckeditorOptions(
    'elfinder',
    [
        'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
        'inline' => false, //по умолчанию false
        'filter' => ['image', 'application/pdf', 'text', 'video'],    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    ]
);

$inputFileOptions = [
    'language' => 'lv',
    'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'filter' => ['video'], // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
    'options' => ['class' => 'form-control'],
    'buttonOptions' => ['class' => 'btn btn-default'],
    'multiple' => false, // возможность выбора нескольких файлов
]
?>

<div class="lectures-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
        <?= $form->field($model, 'value')->textInput() ?>
        <?= $form->field($model, 'description')->textarea() ?>
        <?= $form->field($model, 'type')->dropDownList($tagTypeSelectOptions, ['prompt' => '']) ?>
    </div>
    <hr />
    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
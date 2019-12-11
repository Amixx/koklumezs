<?php

use mihaildev\elfinder\InputFile;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lecturesfiles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lecturesfiles-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'title')->textInput()?>

    <?=$form->field($model, 'lecture_id')
->dropDownList(
    $lectures, // Flat array ('id'=>'label')
    ['prompt' => '']// options
);?>

    <?=$form->field($model, 'file')->widget(InputFile::className(), [
    'language' => 'lv',
    'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'filter' => ['image', 'application/pdf', 'text', 'video', 'audio', 'word', 'text/plain', 'application'], // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
    'options' => ['class' => 'form-control'],
    'buttonOptions' => ['class' => 'btn btn-default'],
    'multiple' => false, // возможность выбора нескольких файлов
]);?>

    <?=$form->field($model, 'thumb')->widget(InputFile::className(), [
    'language' => 'lv',
    'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
    'filter' => ['image'], // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
    'options' => ['class' => 'form-control'],
    'buttonOptions' => ['class' => 'btn btn-default'],
    'multiple' => false, // возможность выбора нескольких файлов
]);?>

    <div class="form-group">
        <?=Html::submitButton('Saglabāt', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>

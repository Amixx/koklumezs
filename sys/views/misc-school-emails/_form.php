<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
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

    <?php if (isset($emailType)) { ?>
        <h3><?= $emailType ?></h3>
    <?php } ?>

    <?php if (isset($possibleEmailTypes)) {
        echo $form->field($model, 'type')
            ->dropDownList($possibleEmailTypes, ['prompt' => '']);
    } ?>

    <?= $form->field($model, 'value')->widget(CKEditor::class, [
        'editorOptions' => $ckeditorOptions,
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
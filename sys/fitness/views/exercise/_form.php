<?php

/* @var \app\fitness\models\Exercise $model */

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
];

$selectedTagIds = isset($selectedTagIds) ? $selectedTagIds : null;
$tagSelected = function ($tagId) use ($selectedTagIds) {
    if (!$selectedTagIds) return false;
    return in_array($tagId, $selectedTagIds);
}
?>

<div class="lectures-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'description')->textarea() ?>
        <?= $form->field($model, 'is_pause')->checkbox() ?>
        <?= $form->field($model, 'needs_evaluation')->checkbox() ?>
        <?= $form->field($model, 'is_archived')->checkbox() ?>
        <?= $form->field($model, 'is_bodyweight')->checkbox() ?>
        <?= $form->field($model, 'is_ready')->checkbox() ?>
        <hr>
        <?= $form->field($model, 'has_reps')->checkbox() ?>
        <?= $form->field($model, 'has_weight')->checkbox() ?>
        <?= $form->field($model, 'has_time')->checkbox() ?>
        <?= $form->field($model, 'has_resistance_bands')->checkbox() ?>
        <?= $form->field($model, 'has_mode')->checkbox() ?>
        <?= $form->field($model, 'has_incline_percent')->checkbox() ?>
        <?= $form->field($model, 'has_pace')->checkbox() ?>
        <?= $form->field($model, 'has_speed')->checkbox() ?>
        <?= $form->field($model, 'has_pulse')->checkbox() ?>
        <?= $form->field($model, 'has_height')->checkbox() ?>
        <hr>
        <?= $form->field($model, 'popularity_type')->dropDownList(
            [
                'POPULAR' => Yii::t('app', 'Popular'),
                'AVERAGE' => Yii::t('app', 'Average popularity'),
                'RARE' => Yii::t('app', 'Rare'),
            ],
            ['prompt' => '']) ?>
        <?= $form->field($model, 'video')->widget(InputFile::class, $inputFileOptions); ?>
        <?= $form->field($model, 'technique_video')->widget(InputFile::class, $inputFileOptions); ?>

        <div class="form-group">
            <label class="control-label" for="tags"> <?= \Yii::t('app', 'Tags') ?></label>
            <select id="tags" class="form-control" name="tags[]" aria-required="true" aria-invalid="false" multiple>
                <option value=""></option>
                <?php foreach ($tags as $tag) { ?>
                    <option value="<?= $tag['id'] ?>" <?= $tagSelected($tag['id']) ? 'selected' : '' ?>><?= $tag['value'] ?></option>
                <?php } ?>
            </select>
            <div class="help-block"></div>
        </div>

        <?php if(isset($interchangeableExerciseSelectedOptions)) { ?>
            <div class="form-group">
                <label class="control-label" for="interchangeable-exercises"> <?= \Yii::t('app', 'Interchangeable exercises') ?></label>
                <select id="interchangeable-exercises" class="form-control" name="interchangeableExercises[]" aria-required="true" aria-invalid="false" multiple>
                    <option value=""></option>

                    <?php foreach ($interchangeableExerciseSelectedOptions as $option) { ?>
                        <option value="<?= $option['id'] ?>" selected><?= $option['text'] ?></option>
                    <?php } ?>
                </select>
                <div class="help-block"></div>
            </div>
        <?php } ?>
    </div>
    <hr/>
    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
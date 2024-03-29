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
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                <?= \Yii::t('app', 'Lesson') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="params-tab" data-toggle="tab" href="#params" role="tab" aria-controls="params" aria-selected="false">
                <?= \Yii::t('app', 'Parameters') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="false">
                <?= \Yii::t('app', 'Files') ?>
            </a>
        </li>
        <?php if ($isUpdate) {
            $action = $assignmentMessage ? "update" : "create";
            $text = $assignmentMessage ? 'Update automatic assignment message' :  'Create automatic assignment message';
            $deleteButton = $assignmentMessage ? 'Delete automatic assignment message' : "";
        ?>
            <li class="nav-item">
                <a href="<?= Url::to(["lesson-assignment-messages/$action", 'lessonId' => $model->id]) ?>">
                    <?= Yii::t('app', $text) ?>
                </a>

            </li>
            <?php if ($assignmentMessage) { ?>
                <li class="nav-item">
                    <a href="<?= Url::to(["lesson-assignment-messages/delete", 'lessonId' => $model->id]) ?>">
                        <?= Yii::t('app', 'Delete automatic assignment message') ?>
                    </a>
                </li>
        <?php }
        } ?>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?= $form->field($model, 'title')->textInput() ?>
            <?= $form->field($model, 'description')->widget(CKEditor::class, [
                'editorOptions' => $ckeditorOptions,
            ]) ?>
            <?= $form->field($model, 'file')->widget(InputFile::class, $inputFileOptions); ?>
            <?= $form->field($model, 'play_along_file')->widget(InputFile::class, $inputFileOptions); ?>
            <?= $form->field($model, 'lang')
                ->dropDownList(
                    [
                        'lv' => Yii::t('app',  'Latvian'),
                        'eng' => Yii::t('app',  'English')
                    ],
                    ['prompt' => '']
                ) ?>
            <?= $form->field($model, 'is_pause')->dropDownList([
                0 => Yii::t('app',  'No'),
                1 => Yii::t('app',  'Yes')
            ], [
                'prompt' => '',
                'value' => $model['is_pause'] ? 1 : 0
            ]) ?>
            <?= $this->render('related', ['lectures' => $lectures, 'relatedLectures' => $relatedLectures]) ?>
            <small><?= \Yii::t('app', 'If you need to add more files, go to section "Files"') ?></small><br /><br />
        </div>
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <?php if ($difficulties) {  ?>
                <?= $this->render('difficulties', ['difficulties' => $difficulties, 'lectureDifficulties' => $lectureDifficulties]) ?>
            <?php } ?>
        </div>
        <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
            <?php $link = Yii::$app->urlManager->createAbsoluteUrl(['lecturesfiles/create', 'lecture_id' => $model->id]) ?>
            <?= $this->render('files', ['lecturefiles' => $lecturefiles, 'link' => $link]) ?>
        </div>
    </div>
    <hr />
    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
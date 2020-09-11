<?php

use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use mihaildev\elfinder\InputFile;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Lectures;
/* @var $this yii\web\View */
/* @var $model app\models\Lectures */
/* @var $form yii\widgets\ActiveForm */

$ckeditorOptions = ElFinder::ckeditorOptions(
    'elfinder',
    [
        'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
        'inline' => false, //по умолчанию false
        'filter' => ['image', 'application/pdf', 'text', 'video'],    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
    ]
);
?>

<div class="lectures-form">

    <?php $form = ActiveForm::begin(); ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?= \Yii::t('app', 'Lesson') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="params-tab" data-toggle="tab" href="#params" role="tab" aria-controls="params" aria-selected="false"><?= \Yii::t('app', 'Parameters') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="evaluations-tab" data-toggle="tab" href="#evaluations" role="tab" aria-controls="evaluations" aria-selected="false"><?= \Yii::t('app', 'Evaluations') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="false"><?= \Yii::t('app', 'Files') ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="related-tab" data-toggle="tab" href="#related" role="tab" aria-controls="related" aria-selected="false"><?= \Yii::t('app', 'Related lessons') ?></a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?= $form->field($model, 'title')->textInput() ?>
            <?= $form->field($model, 'description')->widget(CKEditor::className(), [
                'editorOptions' => $ckeditorOptions,
            ]) ?>
            <?= $form->field($model, 'file')->widget(InputFile::className(), [
                'language' => 'lv',
                'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                'filter' => ['video'], // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
                'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                'options' => ['class' => 'form-control'],
                'buttonOptions' => ['class' => 'btn btn-default'],
                'multiple' => false, // возможность выбора нескольких файлов
            ]); ?>
            <small><?= \Yii::t('app', 'If you need to add more files, go to section "Files"') ?></small><br /><br />
        </div>
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <?php if ($difficulties) {  ?>
                <?= $this->render('difficulties', ['difficulties' => $difficulties, 'lectureDifficulties' => $lectureDifficulties]) ?>
            <?php } ?>
        </div>
        <div class="tab-pane fade" id="evaluations" role="tabpanel" aria-labelledby="evaluations-tab">
            <?php if ($evaluations) {  ?>
                <?= $this->render('evaluations', ['evaluations' => $evaluations, 'lectureEvaluations' => $lectureEvaluations]) ?>
            <?php } ?>
        </div>
        <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
            <?php $link = Yii::$app->urlManager->createAbsoluteUrl(['lecturesfiles/create', 'lecture_id' => $model->id]) ?>
            <?= $this->render('files', ['lecturefiles' => $lecturefiles, 'link' => $link]) ?>
        </div>
        <div class="tab-pane fade" id="related" role="tabpanel" aria-labelledby="related-tab">
            <?= $this->render('related', ['lectures' => $lectures, 'relatedLectures' => $relatedLectures]) ?>
        </div>
    </div>
    <hr />
    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
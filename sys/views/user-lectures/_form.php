<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\UserLectures */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="user-lectures-form">

    <?php $form = ActiveForm::begin(); ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                <?= \Yii::t('app',  'Lesson selection') ?>
            </a>
        </li>
        <?php if ($model->user_id) { ?>
            <li class="nav-item">
                <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">
                    <?= \Yii::t('app',  'Lesson history') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="lecture-tab" data-toggle="tab" href="#lecture" role="tab" aria-controls="lecture" aria-selected="false">
                    <?= \Yii::t('app',  'Lesson') ?>
                </a>
            </li>
        <?php } ?>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <h2><?= \Yii::t('app',  'Student selection') ?></h2>
            <hr />
            <?= $form->field($model, 'user_id')
                ->dropDownList(
                    $students,
                    ['prompt' => '']
                ); ?>
            <?php if ($difficulties && !$hideParams) {  ?>
                <?= $this->render('difficulties', ['difficulties' => $difficulties, 'selected' => $selected]) ?>
                <?= $this->render('seasons', ['seasons' => $seasons, 'seasonSelected' => $seasonSelected]) ?>
            <?php } ?>
        </div>
        <?php if ($model->user_id) { ?>
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <?= $this->render('history', [
                    'lastLectures' => $lastLectures,
                    'userLecturesTimes' => $userLecturesTimes,
                    'difficulties' => $difficulties,
                    'lectureDifficulties' => $lectureDifficulties
                ]) ?>
            </div>
            <div class="tab-pane fade" id="lecture" role="tabpanel" aria-labelledby="lecture-tab">
                <h2><?= \Yii::t('app',  'Lesson selection') ?></h2>
                <hr />
                <?= $lectures ? $form->field($model, 'lecture_id')
                    ->dropDownList(
                        $lectures,
                        ['prompt' => '']
                    ) : '' ?>
            </div>
        <?php } ?>
        <hr />
        <div class="form-group">
            <?= $form->field($model, 'evaluated')->checkBox(['value' => 1]) ?>
            <?= !$outofLectures ? Html::submitButton(
                $lectures
                    ? \Yii::t('app',  'Save')
                    : \Yii::t('app',  'Select lessons'),
                ['class' => 'btn btn-success']
            )
                : Html::a(
                    \Yii::t('app',  'Back'),
                    Url::to(['user-lectures/create']),
                    ['class' => 'btn btn-success']
                ) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
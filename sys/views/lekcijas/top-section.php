<?php

use yii\helpers\Html;

$heartClasses = $uLecture && $uLecture->is_favourite
    ? 'glyphicon-heart LectureEvaluations__Heart--active'
    : 'glyphicon-heart-empty';

$urlToNextLesson = "lekcijas/lekcija/$nextLessonId";

?>

<h3 class="text-center hidden-xs">
    <?= $title ?>
</h3>

<div class="LessonTop">
    <div class="evaluation-and-favorite">
        <div>
            <?= $this->render("amount-evaluation", [
                'difficultyEvaluation' => $difficultyEvaluation,
                'force' => $force,
                'redirectLessonId' => null,
            ]) ?>
        </div>
        <div>
            <?= Html::beginForm(["/lekcijas/toggle-is-favourite?lectureId=$uLecture->lecture_id"], 'get') ?>
            <label for="heart" class="LectureEvaluations__FavouriteText">
                <button type="submit" class="removeBtnStyle"><span class="glyphicon LectureEvaluations__Heart <?= $heartClasses ?>"></span></button>
                <?= \Yii::t('app', 'Add to favourite lessons'); ?>
            </label>
            <?= Html::endForm() ?>
        </div>
    </div>
    <div>
        <?php
        echo $this->render("@app/views/audio-recorder/index");
        ?>
    </div>
    <div class="pull-right">
        <div style="position:relative; display:inline-block">
            <?php if ($lecturefiles) { ?>
                <button type="button" class="btn btn-orange dropdown-toggle hidden-xs" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <?= \Yii::t('app', 'Lyrics and notes'); ?>
                </button>
                <div class="dropdown-menu dropdown-menu-lg-left">
                    <?= $files = $this->render('docs', ['lecturefiles' => $lecturefiles]); ?>
                </div>
            <?php } ?>
        </div>
        <div class="next-lesson" style="display:inline-block">
            <?php if (!$hasEvaluatedLesson) {
                $modalType = "next-lesson";
                echo $this->render('alertEvaluation', [
                    'idPostfix' => $modalType,
                    'force' => $force,
                    'difficultyEvaluation' => $difficultyEvaluation,
                    'redirectLessonId' => $nextLessonId,
                ]);
                if ($nextLessonId) { ?>
                    <button type="button" class="btn btn-orange" data-toggle="modal" data-target="#alertEvaluation-<?= $modalType ?>">
                        <?= \Yii::t('app',  'Next lesson'); ?>
                    </button>
            <?php }
            } else if ($nextLessonId) {
                echo Html::a(\Yii::t('app', 'Next lesson'), [$urlToNextLesson], ['class' => 'btn btn-orange']);
            } ?>
        </div>
    </div>
    <?php if ($showChangeTaskButton) { ?>
        <div class="pull-right">
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#change-task-modal">
                <?= \Yii::t('app',  'I have already played this task'); ?>
            </button>
        </div>

    <?= $this->render("@app/views/shared/modal", [
            'id' => 'change-task-modal',
            'title' => \Yii::t('app', 'Change of task'),
            'bodyFileName' => "/lekcijas/change-task-modal-body",
            'bodyFileParams' => [
                'uLecture' => $uLecture,
            ],
        ]);
    } ?>
    <div class="pull-right next-lesson">
        <button type="button" class="btn btn-blue" data-toggle="modal" data-target="#need-help-modal">
            <?= \Yii::t('app',  'I need help'); ?>
        </button>

        <?= $this->render("@app/views/shared/modal", [
            'id' => 'need-help-modal',
            'title' => \Yii::t('app', 'I need help with this task'),
            'bodyFileName' => "/lekcijas/help-modal-body"
        ]); ?>
    </div>
</div>
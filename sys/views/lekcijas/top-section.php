<?php

use yii\helpers\Html;

$heartClasses = $uLecture && $uLecture->is_favourite
    ? 'glyphicon-heart LectureEvaluations__Heart--active'
    : 'glyphicon-heart-empty';

$urlToNextLesson = "lekcijas/lekcija/$nextLessonId";

$modalBodyText = Yii::t('app', 'Hey! Write to us that did not reach you or remained unclear ... We will be happy to help you understand the task!');
$modalErrorText = Yii::t('app', 'Enter a message');
$modalButtonText = Yii::t('app', 'Submit message');

$helpModalBody = <<<EOD
<div class='form-group'>
    <label for='need-help-message'>$modalBodyText</label>
    <textarea
        class='form-control rounded-0'
        rows="5"
        name='need-help-message'
        id='need-help-message'
    ></textarea>
</div>
<p class='alert alert-danger' id='need-help-error'>$modalErrorText!</p>
<div style='text-align:right'>
    <button
        class='btn btn-orange'
        id='submit-need-help-message'
        data-lessonid='$uLecture->lecture_id'
    >$modalButtonText</button>
</div>
EOD;

$formText = Yii::t('app', 'Change task');
$form = Html::beginForm(["/lekcijas/request-different-lesson?lessonId=$uLecture->lecture_id"])
    . "<button type=\"submit\" class=\"btn btn-blue\" style=\"margin: 4px;\">$formText</button>"
    . Html::endForm();
$modalText = Yii::t('app', 'Hey! Have you played such a task before, or maybe you just dont like it? Click "change task" and I\'ll give you another task of similar complexity. Good luck!');

$changeTaskModalBody = <<<EOD
<div class="text-center">
    <p>$modalText</p>
    $form
</div>
EOD;

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
            'body' => $changeTaskModalBody
        ]);
    } ?>
    <div class="pull-right next-lesson">
        <button type="button" class="btn btn-blue" data-toggle="modal" data-target="#need-help-modal">
            <?= \Yii::t('app',  'I need help'); ?>
        </button>

        <?= $this->render("@app/views/shared/modal", [
            'id' => 'need-help-modal',
            'title' => \Yii::t('app', 'I need help with this task'),
            'body' => $helpModalBody
        ]); ?>
    </div>
</div>
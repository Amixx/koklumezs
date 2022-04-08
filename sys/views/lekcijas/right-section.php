<?php

use app\helpers\ThumbnailHelper;

$thumbStyle = ThumbnailHelper::getThumbnailStyle($model->play_along_file, $videoThumb);

$playAlongTitle = $isFitnessSchool ? 'How to use equipment' : 'Let\'s play together'
?>

<div>
    <div class="text-center">
        <?php if ($model->play_along_file) { ?>
            <h4><?= Yii::t('app', $playAlongTitle); ?>?</h4>
            <div>
                <div class="text-center lecture-wrap lecture-wrap-related">
                    <a class="lecture-thumb" data-toggle="modal" data-target="#lesson_modal_right_<?= $model->id ?>" style="<?= $thumbStyle ?>"></a>
                </div>
                <?= $this->render('view-lesson-modal', [
                    'videoThumb' => $videoThumb,
                    'lecturefiles' => [0 => ['title' => $model->title . " izspēle", 'file' => $model->play_along_file]],
                    'id' => 'right_' . $model->id,
                ]) ?>
            </div>
        <?php } ?>

    </div>
    <?php if ($relatedLectures) { ?>
        <?= $this->render('related', [
            'relatedLectures' => $relatedLectures,
            'lecturefiles' => $lecturefiles,
            'userEvaluatedLectures' => $userEvaluatedLectures,
            'videoThumb' => $videoThumb,
            'modalIdPrefix' => 'desk_',
        ]) ?>
    <?php } ?>
</div>
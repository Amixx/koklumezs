<?php

use app\helpers\ThumbnailHelper;

$thumbStyle = ThumbnailHelper::getThumbnailStyle($workoutExerciseSet->exercise->technique_video, $videoThumb);
?>

<div>
    <div class="text-center">
        <?php if ($workoutExerciseSet->exercise->technique_video) { ?>
            <h4><?= Yii::t('app', 'Exercise technique'); ?></h4>
            <div>
                <div class="text-center lecture-wrap lecture-wrap-related">
                    <a class="lecture-thumb" data-toggle="modal" data-target="#lesson_modal_right_<?= $workoutExerciseSet->exercise->id ?>" style="<?= $thumbStyle ?>"></a>
                </div>
                <?= $this->render('view-lesson-modal', [
                    'videoThumb' => $videoThumb,
                    'lecturefiles' => [0 => ['title' => $workoutExerciseSet->exercise->name . " izspēle", 'file' => $workoutExerciseSet->exercise->technique_video]],
                    'id' => 'right_' . $workoutExerciseSet->exercise->id,
                ]) ?>
            </div>
        <?php } ?>
    </div>
</div>
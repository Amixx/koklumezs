<div class="visible-xs">
    <?php

    use app\helpers\ThumbnailHelper;

    $thumbStyle = ThumbnailHelper::getThumbnailStyle($workoutExercise->exercise->technique_video, $videoThumb);

    $width = $workoutExercise->exercise->technique_video ? "38%" : "100%";
    $marginTop = $workoutExercise->exercise->technique_video ? "60px" : "0px";
    $btnClass = "btn btn-orange dropdown-toggle";
    if ($workoutExercise->exercise->technique_video) {
        $btnClass .= " btn-narrow";
    }
    ?>
    <div style="display: inline-block; width:<?= $width ?>; vertical-align:top; margin-top:8px; position:relative">
        <?php if (isset($lecturefiles['docs'])) { ?>
            <button type="button" class="<?= $btnClass ?>" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                <?= \Yii::t('app', 'Lyrics and notes'); ?>
            </button>
            <div class="dropdown-menu dropdown-menu-lg-left" style="top:unset;">
                <?= $files = $this->render('docs', ['lecturefiles' => $lecturefiles['docs']]); ?>
            </div>
        <?php } ?>
        <p style="color:black; margin: <?= $marginTop ?> 0 0 6px"><?= Yii::t('app', 'Previous assignments in this lesson') ?></p>
    </div>


    <?php if ($workoutExercise->exercise->technique_video) { ?>
        <div style="display: inline-block; width:60%;">
            <div>
                <div class="lecture-wrap">
                    <a class="lecture-thumb" data-toggle="modal" data-target="#lesson_modal_mob_<?= $workoutExercise->exercise->id ?>" style="<?= $thumbStyle ?>"></a>
                    <span class="lecture-title"><?= Yii::t('app', 'Exercise technique'); ?></span>
                </div>
                <?= $this->render('view-lesson-modal', [
                    'videoThumb' => $videoThumb,
                    'lecturefiles' => [0 => ['title' => $workoutExercise->exercise->name . " tehnika", 'file' => $workoutExercise->exercise->technique_video]],
                    'id' => "mob_" . $workoutExercise->exercise->id,
                ]) ?>
            </div>
        </div>
    <?php } ?>
</div>
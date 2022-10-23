<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Lesson') . ': ' . $workoutExerciseSet->exerciseSet->exercise->name;

$hasWeight = !!$workoutExerciseSet->weight;
$hasReps = !!$workoutExerciseSet->exerciseSet->reps;
$hasTime = !!$workoutExerciseSet->exerciseSet->time_seconds;

function wrapInBold($str) {
    return "<strong>$str</strong>";
}

$repsWeightTimeFormatted = '';
if($hasReps) {
    $repsWeightTimeFormatted = wrapInBold($workoutExerciseSet->exerciseSet->reps) . ' reizes';
} else if($hasTime) {
    $repsWeightTimeFormatted = wrapInBold($workoutExerciseSet->exerciseSet->time_seconds) . ' sekundes';
}
if($hasWeight) {
    $repsWeightTimeFormatted .= ' ar ' . wrapInBold($workoutExerciseSet->weight) . ' kg svaru';
}
?>
<!-- unpkg : use the latest version of Video.js -->
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>
<div class="row">
    <div class="top-and-left-section">
        <div class="border-left col-md-7">
            <div class="lesson-column lesson-column-middle wrap-overlay">
                <div class="row">
                    <div class="col-md-12">
                        <h1><?= $workoutExerciseSet->exerciseSet->exercise->name; ?></h1>
                        <?php if ($workoutExerciseSet->exerciseSet->exercise->description) { ?>
                            <p><?= $workoutExerciseSet->exerciseSet->exercise->description ?></p>
                        <?php } ?>
                    </div>
                    <div>
                        <div class="col-md-12" style="font-size: 16px; margin-left: 8px; margin-bottom: 16px;">
                            <?= $repsWeightTimeFormatted ?>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom:16px; text-align:center">
                        <?php if (!$workoutExerciseSet->exerciseSet->exercise->is_pause) { ?>
                            <div><?= $this->render("amount-evaluation", ['difficultyEvaluation' => $difficultyEvaluation]) ?></div>
                        <?php } ?>
                        <?php if ($difficultyEvaluation || $workoutExerciseSet->exerciseSet->exercise->is_pause) { ?>
                            <?php if ($nextWorkoutExercise) { ?>
                                <?= Html::a(
                                    \Yii::t('app', 'Next exercise'),
                                    ["fitness-student-exercises/view?id=$nextWorkoutExercise->id"],
                                    ['class' => 'btn btn-orange', 'style' => 'margin-top:8px;']
                                ); ?>
                            <?php } else { ?>
                                <?= Html::a(
                                    \Yii::t('app', 'Finish workout'),
                                    ["fitness-student-exercises/workout-summary", 'workoutId' => $workoutExerciseSet->workout_id],
                                    ['class' => 'btn btn-orange', 'style' => 'margin-top:8px;']
                                ); ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <?php if (!empty($equipmentVideos)) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class='text-center'><?= Yii::t('app', 'How to use the equipment for next exercises') ?></h4>
                            <ul class='equipment-video'>
                                <?php foreach ($equipmentVideos as $key => $video) { ?>
                                    <?= $this->render(
                                        'video',
                                        [
                                            'lectureVideoFiles' => [0 => ['title' => Yii::t('app', 'How to use equipment'), 'file' => $video]],
                                            'thumbnail' => $videoThumb ?? '',
                                            'idPrefix' => 'equipment-vid-' . $key,
                                        ]
                                    ); ?>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>


                <?php if ($workoutExerciseSet->exerciseSet->video) { ?>
                    <?= $this->render(
                        'video',
                        [
                            'lectureVideoFiles' => [0 => ['title' => $workoutExerciseSet->exerciseSet->exercise->name, 'file' => $workoutExerciseSet->exerciseSet->video]],
                            'thumbnail' => $videoThumb ?? '',
                            'idPrefix' => 'fitness_main',
                        ]
                    ); ?>
                <?php } ?>

                <?php if ($workoutExerciseSet->exerciseSet->exercise->technique_video) {
                    echo $this->render('mob-related-section', [
                        'workoutExerciseSet' => $workoutExerciseSet->exerciseSet,
                        'videoThumb' => $videoThumb,
                    ]);
                } ?>
            </div>
        </div>
    </div>
    <?php if ($workoutExerciseSet->exerciseSet->exercise->technique_video) { ?>
        <div class="col-md-3 hidden-xs">
            <div class="lesson-column lesson-column-right wrap-overlay">
                <?= $this->render("right-section.php", [
                    'videoThumb' => $videoThumb,
                    'workoutExerciseSet' => $workoutExerciseSet->exerciseSet,
                ]) ?>
            </div>
        </div>
    <?php } ?>
</div>
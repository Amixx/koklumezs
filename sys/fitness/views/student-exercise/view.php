<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Exercise') . ': ' . $workoutExerciseSet->exerciseSet->exercise->name;
?>
<!-- unpkg : use the latest version of Video.js -->
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>

<div class="row">
    <div class="col-sm-12 view-workout">
        <div class="view-workout__main-info">
            <h1><?= $workoutExerciseSet->exerciseSet->exercise->name; ?></h1>
            <p class="description"><?= $workoutExerciseSet->repsWeightTimeFormatted() ?></p>
            <div>
                <?php if ($workoutExerciseSet->exerciseSet->video) { ?>
                    <?= $this->render(
                        'video',
                        [
                            'fileUrl' => $workoutExerciseSet->exerciseSet->video,
                            'thumbnail' => $videoThumb,
                            'id' => 'fitness_main',
                        ]
                    ); ?>
                <?php } ?>
            </div>
            <div class="view-workout__actions">
                <?php if (!$workoutExerciseSet->exerciseSet->exercise->is_pause) { ?>
                    <div><?= $this->render(
                            "amount-evaluation",
                            ['difficultyEvaluation' => $difficultyEvaluation]) ?>
                    </div>
                <?php } ?>
                <?php if ($difficultyEvaluation || $workoutExerciseSet->exerciseSet->exercise->is_pause) { ?>
                    <?php
                    $btnText = \Yii::t('app', $nextWorkoutExercise ? 'Next exercise' : 'Finish workout');
                    $btnLink = $nextWorkoutExercise
                        ? ["fitness-student-exercises/view", 'id' => $nextWorkoutExercise->id]
                        : ["fitness-student-exercises/workout-summary", 'workoutId' => $workoutExerciseSet->workout_id];
                    echo Html::a(
                        $btnText,
                        $btnLink,
                        ['class' => 'btn btn-success exercise-action-btn']
                    ); ?>
                <?php } ?>
            </div>
        </div>
        <div class="view-workout__helpful-content">
            <?php if ($workoutExerciseSet->exerciseSet->exercise->description) { ?>
                <p><?= $workoutExerciseSet->exerciseSet->exercise->description ?></p>
            <?php } ?>
            <?php if ($workoutExerciseSet->exerciseSet->exercise->technique_video) { ?>
                <?= $this->render(
                    'video',
                    [
                        'fileUrl' => $workoutExerciseSet->exerciseSet->exercise->technique_video,
                        'thumbnail' => $videoThumb,
                        'id' => 'fitness_technique',
                    ]
                ); ?>
            <?php } ?>
        </div>
        <ul class="view-workout__exercise-list">
            <?php
            $passedCurrentExercise = false;
            foreach($workoutExerciseSet->workout->workoutExerciseSets as $wes) {
                $class = '';
                if($passedCurrentExercise) {
                    $class = 'future';
                } else if($wes->id === $workoutExerciseSet->id) {
                    $passedCurrentExercise = true;
                    $class = 'current';
                } else {
                    $class = 'past';
                }
                ?>
                <li class="<?= $class ?>">
                    <div class="view-workout__other-exercise-item">
                        <span><?= $wes->exerciseSet->exercise->name ?></span>
                        <?php if($class === 'future' && $wes->exerciseSet->exercise->technique_video) { ?>
                            <button class="btn btn-primary fitness-toggle-technique-vid">
                                <span class="glyphicon glyphicon-menu-down"></span>
                            </button>
                        <?php } ?>
                    </div>
                    <div class="hidden">
                        <?= $this->render(
                            'video',
                            [
                                'fileUrl' => $wes->exerciseSet->exercise->technique_video,
                                'thumbnail' => $videoThumb,
                                'id' => 'fitness_other_ex_technique_' . $wes->id,
                            ]
                        ); ?>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

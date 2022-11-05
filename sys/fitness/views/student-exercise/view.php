<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Exercise') . ': ' . $workoutExercise->exercise->name;
?>
<!-- unpkg : use the latest version of Video.js -->
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>

<div class="row">
    <div class="col-sm-12 view-workout">
        <div class="view-workout__main-info">
            <h1><?= $workoutExercise->exercise->name; ?></h1>
            <?php if ($workoutExercise->exercise->is_pause) { ?>
                <div>
                    <div class="base-timer">
                        <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <g class="base-timer__circle">
                                <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
                                <path
                                        id="base-timer-path-remaining"
                                        stroke-dasharray="283"
                                        class="base-timer__path-remaining"
                                        d="
                                          M 50, 50
                                          m -45, 0
                                          a 45,45 0 1,0 90,0
                                          a 45,45 0 1,0 -90,0
                                        "
                                ></path>
                            </g>
                        </svg>
                        <span id="base-timer-label" class="base-timer__label">
                            <?= $workoutExercise->timeFormatted() ?>
                        </span>
                    </div>
                </div>
            <?php } ?>
            <?php if (!$workoutExercise->exercise->is_pause) { ?>
                <p class="description"><?= $workoutExercise->repsWeightTimeFormatted() ?></p>
            <?php } ?>
            <div>
                <?php
                $vid = $workoutExercise->videoToDisplay();
                if ($vid) { ?>
                    <?= $this->render(
                        'video',
                        [
                            'fileUrl' => $vid,
                            'thumbnail' => $videoThumb,
                            'id' => 'fitness_main',
                        ]
                    ); ?>
                <?php } ?>
            </div>
            <div class="view-workout__actions">
                <?php if ($workoutExercise->exercise->renderEvaluation()) { ?>
                    <div>
                        <?= $this->render(
                            "amount-evaluation",
                            [
                                'difficultyEvaluation' => $difficultyEvaluation,
                                'reps' => $workoutExercise->reps,
                                'timeSeconds' => $workoutExercise->time_seconds,
                            ]) ?>
                    </div>
                <?php } ?>
                <?php if ($difficultyEvaluation || !$workoutExercise->exercise->renderEvaluation()) { ?>
                    <?php
                    $btnText = \Yii::t('app', $nextWorkoutExercise ? 'To next exercise' : 'Finish workout');
                    $btnLink = $nextWorkoutExercise
                        ? ["fitness-student-exercises/view", 'id' => $nextWorkoutExercise->id]
                        : ["fitness-student-exercises/workout-summary", 'workoutId' => $workoutExercise->workout_id];
                    echo Html::a(
                        $btnText,
                        $btnLink,
                        ['class' => 'btn btn-success exercise-action-btn']
                    ); ?>
                <?php } ?>

                <?php if ($nextWorkoutExercise && $workoutExercise->exercise->renderEvaluation()) { ?>
                    <div style="margin-top: 24px;">
                        <h4 style="margin-bottom: 8px;">Nākamais vingrojums</h4>
                        <p><?= $nextWorkoutExercise->exercise->name ?>
                            - <?= $nextWorkoutExercise->repsWeightTimeFormatted() ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="view-workout__helpful-content">
            <?php if ($workoutExercise->exercise->description) { ?>
                <p><?= $workoutExercise->exercise->description ?></p>
            <?php } ?>
            <?php if ($workoutExercise->exercise->technique_video) { ?>
                <?= $this->render(
                    'video',
                    [
                        'fileUrl' => $workoutExercise->exercise->technique_video,
                        'thumbnail' => $videoThumb,
                        'id' => 'fitness_technique',
                    ]
                ); ?>
            <?php } ?>
        </div>
        <ul class="view-workout__exercise-list">
            <?php
            $passedCurrentExercise = false;
            foreach ($workoutExercise->workout->workoutExercises as $wExercise) {
                $class = '';
                if ($passedCurrentExercise) {
                    $class = 'future';
                } else if ($wExercise->id === $workoutExercise->id) {
                    $passedCurrentExercise = true;
                    $class = 'current';
                } else {
                    $class = 'past';
                }
                ?>
                <li class="<?= $class ?>">
                    <div class="view-workout__other-exercise-item">
                        <span>
                            <span><?= $wExercise->exercise->name ?></span>
                            <?php if ($wExercise->weight) { ?>
                                <span>(<strong><?= $wExercise->weight ?></strong> kg)</span>
                            <?php } ?>
                        </span>
                        <?php if ($class === 'future' && $wExercise->exercise->technique_video) { ?>
                            <button class="btn btn-primary fitness-toggle-technique-vid">
                                <span class="glyphicon glyphicon-menu-down"></span>
                            </button>
                        <?php } ?>
                    </div>
                    <div class="hidden">
                        <?= $this->render(
                            'video',
                            [
                                'fileUrl' => $wExercise->exercise->technique_video,
                                'thumbnail' => $videoThumb,
                                'id' => 'fitness_other_ex_technique_' . $wExercise->id,
                            ]
                        ); ?>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

<?php

use yii\helpers\Html;

$this->title = \Yii::t('app',  'Lesson') . ': ' . $workoutExercise->exercise->name;

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
                        <h1><?= \Yii::t('app',  'Exercise') . ': ' . $workoutExercise->exercise->name; ?></h1>
                        <!-- description -->
                    </div>
                    <div>
                        <?php if ($workoutExercise->weight) { ?>
                            <div class="col-md-12" style="font-size: 16px; margin-left: 8px; margin-bottom: 16px;">
                                <?= Yii::t('app', 'Weight') ?>: <?= $workoutExercise->weight ?> (kg)
                            </div>
                        <?php } ?>
                        <?php if ($workoutExercise->reps) { ?>
                            <div class="col-md-12" style="font-size: 16px; margin-left: 8px; margin-bottom: 16px;">
                                <?= Yii::t('app', 'Repetitions') ?>: <?= $workoutExercise->reps ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($nextWorkoutExercise) { ?>
                        <div class="col-sm-12" style="margin-bottom: 16px;">
                            <?= Html::a(
                                \Yii::t('app', 'Next exercise'),
                                ["fitness-student-exercises/view?id=$nextWorkoutExercise->id"],
                                ['class' => 'btn btn-orange']
                            ); ?>
                        </div>
                    <?php } else { ?>
                        <div class="col-sm-12" style="margin-bottom: 16px;">
                            <?= Html::a(
                                \Yii::t('app', 'Finish workout'),
                                ["lekcijas/index"],
                                ['class' => 'btn btn-orange']
                            ); ?>
                        </div>
                    <?php } ?>
                </div>

                <?php if (!empty($equipmentVideos)) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class='text-center'><?= Yii::t('app', 'How to use the equipment for next exercises') ?></h3>
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


                <?php if ($workoutExercise->exercise->first_set_video) { ?>
                    <?= $this->render(
                        'video',
                        [
                            'lectureVideoFiles' => [0 => ['title' => $workoutExercise->exercise->name, 'file' => $workoutExercise->exercise->first_set_video]],
                            'thumbnail' => $videoThumb ?? '',
                            'idPrefix' => 'main',
                        ]
                    ); ?>
                <?php } ?>

                <?php if ($workoutExercise->exercise->technique_video) {
                    echo $this->render('mob-related-section', [
                        'workoutExercise' => $workoutExercise,
                        'videoThumb' => $videoThumb,
                    ]);
                } ?>
            </div>
        </div>
    </div>
    <?php if ($workoutExercise->exercise->technique_video) { ?>
        <div class="col-md-3 hidden-xs">
            <div class="lesson-column lesson-column-right wrap-overlay">
                <?= $this->render("right-section.php", [
                    'videoThumb' => $videoThumb,
                    'workoutExercise' => $workoutExercise,
                ]) ?>
            </div>
        </div>
    <?php } ?>
</div>
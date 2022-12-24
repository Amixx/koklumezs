<?php

use app\fitness\models\Workout;
use app\fitness\models\WorkoutExercise;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\ThumbnailHelper;

$this->title = \Yii::t('app', 'My workouts');
?>
<div class="lectures-index">
    <h3><?= $this->title ?></h3>
    <div class="row wrap-overlay" style="padding: 16px 2px; border-radius: 16px; min-height: 100vh;">
        <?php
        if (empty($unfinishedWorkouts)) { ?>
            <div class="col-md-6">
                <h3><?= \Yii::t('app', "You don't have any workouts at the moment") ?>!</h3>
            </div>
        <?php } else {
            foreach ($unfinishedWorkouts as $workout) {
                if (isset($workout["workoutExercises"][0])) {
                    $firstWorkoutExercise = $workout["workoutExercises"][0];
                    $firstExercise = $firstWorkoutExercise["exercise"];
                    $vid = WorkoutExercise::getVideoToDisplay($firstWorkoutExercise['id']);
                    $thumbStyle = ThumbnailHelper::getThumbnailStyle($vid, $videoThumb);

                    $isNew = !$workout['opened_at'];
                    $isUnfinished = !$workout['messageForCoach'] && !$workout['abandoned'];

                    if ($isNew || $isUnfinished) { ?>
                        <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                            <!-- new workouts -->
                            <?php if ($isNew) { ?>
                                <a class="lecture-thumb"
                                   href="<?= Url::to(['fitness-student-exercises/view', 'id' => $workout["workoutExercises"][0]["id"]]) ?>"
                                   style="<?= $thumbStyle ?>"
                                ></a>
                                <!-- unfinished workouts -->
                            <?php } else if ($isUnfinished) {
                                $firstUnopenedexercise = Workout::getFirstUnopenedExercise($workout);
                                ?>
                                <span class="lecture-thumb unfinished" style="<?= $thumbStyle ?>">
                                 <?= Html::a(Yii::t('app', $firstUnopenedexercise ? 'Continue workout' : 'Send message to trainer'), $firstUnopenedexercise ? [
                                     'fitness-student-exercises/view',
                                     'id' => $firstUnopenedexercise->id,
                                 ] : [
                                     'fitness-student-exercises/workout-summary',
                                     'workoutId' => $workout['id'],
                                 ], [
                                     'class' => 'btn btn-success'
                                 ]) ?>
                                 <?= Html::a(Yii::t('app', 'Abandon workout'), [
                                     'fitness-workouts/abandon',
                                     'id' => $workout['id'],
                                 ], [
                                     'class' => 'btn btn-danger',
                                 ]) ?>
                            </span>
                            <?php } ?>
                            <p><?= Yii::t('app', 'First exercise') ?>: <?= $firstExercise["name"] ?></p>
                        </div>
                    <?php }
                }
            }
        } ?>
    </div>
    <?php
    if (isset($pages) && $pages) {
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);
    }
    ?>
</div>
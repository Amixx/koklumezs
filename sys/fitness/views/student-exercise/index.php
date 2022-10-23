<?php

use app\fitness\models\Workout;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\ThumbnailHelper;

$this->title = \Yii::t('app', 'My workouts');
?>
<div class="lectures-index ">
    <h3><?= $this->title ?></h3>
    <div class="row wrap-overlay" style="padding: 16px 2px; border-radius: 16px; min-height: 100vh;">
        <?php
        if (empty($unfinishedWorkouts)) { ?>
            <div class="col-md-6">
                <h3><?= \Yii::t('app', "You don't have any workouts yet") ?>!</h3>
            </div>
        <?php } else {
            foreach ($unfinishedWorkouts as $workout) {
                if (isset($workout["workoutExerciseSets"][0])) {
                    $firstExerciseSet = $workout["workoutExerciseSets"][0]["exerciseSet"];
                    $thumbStyle = ThumbnailHelper::getThumbnailStyle($firstExerciseSet["video"], $videoThumb);
                    ?>
                    <div class="col-md-6 col-lg-3 text-center lecture-wrap">
                        <!-- new workouts -->
                        <?php if (!$workout['opened_at']) { ?>
                            <a class="lecture-thumb"
                               href="<?= Url::to(['fitness-student-exercises/view', 'id' => $workout["workoutExerciseSets"][0]["id"]]) ?>"
                               style="<?= $thumbStyle ?>"
                            ></a>
                            <!-- unfinished workouts -->
                        <?php } else if (!$workout['evaluation']) { ?>
                            <span class="lecture-thumb unfinished" style="<?= $thumbStyle ?>">
                                 <?= Html::a(Yii::t('app', 'Continue workout'), [
                                     'fitness-student-exercises/view',
                                     'id' => Workout::getFirstUnopenedExerciseSet($workout)->id,
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

                        <p><?= Yii::t('app', 'First exercise') ?>: <?= $firstExerciseSet["exercise"]["name"] ?></p>
                    </div>
                    <?php
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
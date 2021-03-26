<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserLectures */

$this->title = \Yii::t('app',  'Assign lesson');
['label' => \Yii::t('app',  'Assigned lessons'), 'url' => ['index']];

?>
<div class="user-lectures-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'students' => $students,
        'lectures' => $lectures,
        'outofLectures' => $outofLectures,
        'lastLectures' => $lastLectures,
        'userLecturesTimes' => $userLecturesTimes,
        'difficulties' => $difficulties,
        'lectureDifficulties' => $lectureDifficulties,
        'selected' => $selected,
        'hideParams' => $hideParams,
        'seasons' => $seasons,
        'seasonSelected' => $seasonSelected,
    ]) ?>

</div>
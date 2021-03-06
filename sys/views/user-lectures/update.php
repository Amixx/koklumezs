<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserLectures */

$this->title =  \Yii::t('app',  'Edit lesson') . ': ' . $model->id;
['label' => \Yii::t('app',  'Assigned lessons'), 'url' => ['index']];
['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
\Yii::t('app',  'Edit');
?>
<div class="user-lectures-update">

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
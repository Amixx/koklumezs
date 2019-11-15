<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserLectures */

$this->title = 'Rediģēt lekciju: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Piešķirtās lekcijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Rediģēt';
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

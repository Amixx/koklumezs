<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = 'Rediģēt nodarbību: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Nodarbības', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Rediģēt';
?>
<div class="lectures-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'difficulties' => $difficulties,
        'handdifficulties' => $handdifficulties,
        'evaluations' => $evaluations,
        'lectureDifficulties' => $lectureDifficulties,
        'lectureHandDifficulties' => $lectureHandDifficulties,
        'lectureEvaluations' => $lectureEvaluations,
        'lecturefiles' => $lecturefiles,
        'lectures' => $lectures,
        'relatedLectures' => $relatedLectures
    ]) ?>

</div>
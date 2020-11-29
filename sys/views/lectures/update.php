<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = \Yii::t('app', 'Edit lesson') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Lessons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('app',  'Edit');
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
        'relatedLectures' => $relatedLectures,
    ]) ?>

</div>
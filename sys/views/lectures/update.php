<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = \Yii::t('app', 'Edit lesson') . ': ' . $model->title;
['label' => \Yii::t('app',  'Lessons'), 'url' => ['index']];
['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
\Yii::t('app',  'Edit');
?>
<div class="lectures-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'difficulties' => $difficulties,
        'evaluations' => $evaluations,
        'lectureDifficulties' => $lectureDifficulties,
        'lectureEvaluations' => $lectureEvaluations,
        'lecturefiles' => $lecturefiles,
        'lectures' => $lectures,
        'relatedLectures' => $relatedLectures,
        'assignmentMessage' => $assignmentMessage,
        'isUpdate' => true,
    ]) ?>

</div>
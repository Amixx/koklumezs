<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = \Yii::t('app',  'Create lesson');
['label' => \Yii::t('app',  'Lessons'), 'url' => ['index']];

?>
<div class="lectures-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'difficulties' => $difficulties,
        'evaluations' => $evaluations,
        'lectureDifficulties' => [],
        'lectureEvaluations' => [],
        'lecturefiles' => [],
        'lectures' => $lectures,
        'relatedLectures' => [],
        'isUpdate' => false,
    ]) ?>

</div>
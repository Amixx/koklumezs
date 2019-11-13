<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = 'Izveidot lekciju';
$this->params['breadcrumbs'][] = ['label' => 'Lekcijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lectures-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'difficulties' =>$difficulties,
        'handdifficulties' => $handdifficulties,
        'evaluations' => $evaluations,
        'lectureDifficulties' => [],
        'lectureHandDifficulties' => [],
        'lectureEvaluations' => [],
        'lecturefiles' => [],
        'lectures' => $lectures, 
        'relatedLectures' => []        
    ]) ?>

</div>

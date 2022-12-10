<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = \Yii::t('app', 'Update progression chain') . ': ' . $model->title;
['label' => \Yii::t('app',  'Progression chains'), 'url' => ['index']];
\Yii::t('app',  'Edit');
?>
<div class="lectures-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'progressionChainExercises' => $progressionChainExercises,
        'exerciseSelectOptions' => $exerciseSelectOptions,
        'mainExercise' => $mainExercise,
        'weightExerciseSelectOptions' => $weightExerciseSelectOptions,
        'exerciseModel' => $exerciseModel,
        'tags' => $tags,
    ]) ?>
</div>
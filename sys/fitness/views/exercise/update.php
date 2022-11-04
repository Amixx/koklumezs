<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = \Yii::t('app', 'Edit exercise') . ': ' . $model->name;
['label' => \Yii::t('app',  'Exercises'), 'url' => ['index']];
['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
\Yii::t('app',  'Edit');
?>
<div class="lectures-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'tags' => $tags,
        'selectedTagIds' => $selectedTagIds,
        'interchangeableExerciseSelectedOptions' => $interchangeableExerciseSelectedOptions,
    ]) ?>

</div>
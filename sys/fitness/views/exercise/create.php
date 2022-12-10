<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = \Yii::t('app',  'Create exercise');
['label' => \Yii::t('app',  'Exercises'), 'url' => ['index']];

?>
<div class="lectures-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'tags' => $tags,
        'interchangeableExercisesSelectValue' => [],
    ]) ?>

</div>
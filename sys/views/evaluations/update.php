<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Evaluations */

$this->title = \Yii::t('app',  'Edit evaluation') . ': ' . $model->title;
['label' => \Yii::t('app',  'Evaluations'), 'url' => ['index']];
['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
\Yii::t('app',  'Edit');
?>
<div class="evaluations-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'stars_texts' => $stars_texts
    ]) ?>

</div>
<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sentlectures */

$this->title = \Yii::t('app',  'Update sent lessons') . ': ' . $model->id;
['label' => \Yii::t('app',  'Sent lessons'), 'url' => ['index']];
['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
\Yii::t('app',  'Edit');
?>
<div class="sentlectures-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
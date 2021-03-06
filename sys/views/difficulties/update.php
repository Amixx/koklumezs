<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */

$this->title = \Yii::t('app', 'Edit parameter') . ': ' . $model->name;
['label' => \Yii::t('app', 'Parameters'), 'url' => ['index']];
['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
\Yii::t('app', 'Edit');
?>
<div class="difficulties-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
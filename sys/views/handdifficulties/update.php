<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Handdifficulties */

$this->title = \Yii::t('app',  'Edit category') . ': ' . $model->id;
 ['label' => \Yii::t('app',  'Categories'), 'url' => ['index']];
 ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
 \Yii::t('app',  'Edit');
?>
<div class="handdifficulties-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
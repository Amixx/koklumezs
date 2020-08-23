<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Handdifficulties */

$this->title = \Yii::t('app',  'Edit category') . ': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('app',  'Edit');
?>
<div class="handdifficulties-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Userlectureevaluations */

$this->title = \Yii::t('app',  'Edit student evaluation') . ': ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Student evaluations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('app',  'Edit');
?>
<div class="userlectureevaluations-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
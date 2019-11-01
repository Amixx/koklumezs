<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Handdifficulties */

$this->title = 'Rediģēt kategoriju: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kategorijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Rediģēt';
?>
<div class="handdifficulties-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

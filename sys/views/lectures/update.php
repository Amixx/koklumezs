<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = 'Rediģēt lekciju: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Lekcijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Rediģēt';
?>
<div class="lectures-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

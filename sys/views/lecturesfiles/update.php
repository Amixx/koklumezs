<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lecturesfiles */

$this->title = 'Rediģēt failu: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Faili', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Rediģēt';
?>
<div class="lecturesfiles-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'lectures' => $lectures
    ]) ?>

</div>

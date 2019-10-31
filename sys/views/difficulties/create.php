<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */

$this->title = 'Izveidot parametru';
$this->params['breadcrumbs'][] = ['label' => 'Parametri', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="difficulties-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

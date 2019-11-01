<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Handdifficulties */

$this->title = 'Izveidot kategoriju';
$this->params['breadcrumbs'][] = ['label' => 'Kategorijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="handdifficulties-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

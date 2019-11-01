<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lecturesfiles */

$this->title = 'Pievienot failu';
$this->params['breadcrumbs'][] = ['label' => 'Faili', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lecturesfiles-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'lectures' => $lectures,
        'get' => $get
    ]) ?>

</div>

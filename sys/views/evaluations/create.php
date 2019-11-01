<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Evaluations */

$this->title = 'Izveidot novērtējumu';
$this->params['breadcrumbs'][] = ['label' => 'Novērtējumi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evaluations-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

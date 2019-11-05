<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = 'Izveidot lietotāju';
$this->params['breadcrumbs'][] = ['label' => 'Lietotāji', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'studentGoals' => $studentGoals,
        'studentHandGoals' => $studentHandGoals,
        'difficulties' => $difficulties,
        'handdifficulties' => $handdifficulties,
    ]) ?>

</div>
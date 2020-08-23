<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = \Yii::t('app',  'Create user');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Users'), 'url' => ['index']];
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
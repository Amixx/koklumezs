<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = \Yii::t('app',  'Edit user') . ': ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('app',  'Edit');

?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'studentGoals' => $studentGoals,
        'studentHandGoals' => $studentHandGoals,
        'difficulties' => $difficulties
    ]) ?>

</div>
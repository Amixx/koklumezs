<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = \Yii::t('app',  'Edit user') . ': ' . $model->email;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->email, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('app',  'Edit');

$subPlans = isset($schoolSubPlans) ? $schoolSubPlans : null;

?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'studentGoals' => $studentGoals,
        'studentHandGoals' => $studentHandGoals,
        'difficulties' => $difficulties,
        'schoolSubPlans' => $subPlans
    ]) ?>

</div>
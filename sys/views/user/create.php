<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = \Yii::t('app',  'Create user');
['label' => \Yii::t('app',  'Users'), 'url' => ['index']];

?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'studentGoals' => $studentGoals,
        'difficulties' => $difficulties,
        'studentSubplan' => $studentSubplan,
    ]) ?>

</div>
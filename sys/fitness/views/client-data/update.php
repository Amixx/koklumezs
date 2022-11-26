<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\fitness\models\ClientData */
/* @var $user app\models\Users */

$this->title = \Yii::t('app', 'Edit client data') . ': ' . "{$user->first_name} {$user->last_name}";
?>

<div class="lectures-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
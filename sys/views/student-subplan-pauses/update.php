<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */

$this->title = \Yii::t('app', 'Edit plan pause') ;
 ['label' => \Yii::t('app', 'Parameters'), 'url' => ['index']];
 \Yii::t('app', 'Edit');
?>
<div class="difficulties-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'userId' => $userId,
    ]) ?>

</div>
<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Create plan pause');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Plan pauses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="difficulties-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
        'userId' => null,
    ]) ?>

</div>
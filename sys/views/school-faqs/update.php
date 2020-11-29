<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Edit FAQ') . ': ' . $model->question;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'FAQs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->question, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('app', 'Edit');
?>
<div class="difficulties-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
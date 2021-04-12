<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Edit FAQ') . ': ' . $model->question;
['label' => \Yii::t('app', 'FAQs'), 'url' => ['index']];
['label' => $model->question, 'url' => ['view', 'id' => $model->id]];
\Yii::t('app', 'Edit');
?>
<div class="difficulties-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
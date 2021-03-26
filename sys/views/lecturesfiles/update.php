<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lecturesfiles */

$this->title = \Yii::t('app',  'Edit file') . ': ' . $model->id;
['label' =>  \Yii::t('app',  'File'), 'url' => ['index']];
['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
\Yii::t('app',  'Edit');
?>
<div class="lecturesfiles-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'lectures' => $lectures
    ]) ?>

</div>
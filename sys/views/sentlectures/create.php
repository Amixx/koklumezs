<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sentlectures */

$this->title = \Yii::t('app',  'Assigned lessons');
 ['label' => \Yii::t('app',  'Sent lessons'), 'url' => ['index']];

?>
<div class="sentlectures-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
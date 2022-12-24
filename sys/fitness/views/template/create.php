<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lectures */

$this->title = \Yii::t('app',  'Create template');
['label' => \Yii::t('app',  'Templates'), 'url' => ['index']];

?>
<div class="lectures-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form') ?>

</div>